<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountRelationship;
use App\Models\OperatorPriceList;
use App\Models\OperatorServiceCatalog;
use App\Models\PackageOffer;
use App\Services\AccountRelationshipsListingService;
use App\Services\OperatorPackageAgencyPriceResolver;
use App\Services\PackageOfferSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class AccountOperatorPackageOfferController extends Controller
{
    public function __construct(
        private readonly AccountRelationshipsListingService $relationshipsListing,
        private readonly OperatorPackageAgencyPriceResolver $packagePriceResolver,
    ) {
    }

    public function agenciesIndex(Request $request): View
    {
        $account = $this->resolveOperatorAccount($request);

        $rows = $this->relationshipsListing->forAccount((int) $account->id, 'operator', 'agency')
            ->filter(fn (array $row): bool => $row['relationship']->status === AccountRelationship::STATUS_APPROVED)
            ->values();

        $agencyIds = $rows->map(fn (array $row): int => (int) $row['counterpart']->id)->all();
        $offerCountsByAgency = $this->offerCountsByAgency((int) $account->id, $agencyIds);

        foreach ($rows as $row) {
            $agencyId = (int) $row['counterpart']->id;
            $offered = $offerCountsByAgency[$agencyId]['offered'] ?? 0;
            $accepted = $offerCountsByAgency[$agencyId]['accepted'] ?? 0;
            $row['relationship']->setAttribute('offered_package_count', $offered);
            $row['relationship']->setAttribute('accepted_package_count', $accepted);
        }

        return view('account.package-offers.operator.agencies', [
            'account' => $account,
            'rows' => $rows,
        ]);
    }

    public function edit(Request $request, Account $agency): View
    {
        $account = $this->resolveOperatorAccount($request);
        $this->assertAgencyRelationship((int) $account->id, (int) $agency->id);

        $packageOffers = PackageOffer::query()
            ->where('operator_id', $account->id)
            ->where('agency_id', $agency->id)
            ->get()
            ->keyBy(fn (PackageOffer $offer) => (int) $offer->operator_service_catalog_id);

        $packages = OperatorServiceCatalog::query()
            ->where('operator_id', $account->id)
            ->where('status', 'active')
            ->whereHas('items', fn ($q) => $q->where('inclusion_mode', 'included'))
            ->with(['translations.language.locale', 'items'])
            ->withCount('items')
            ->orderByDesc('id')
            ->get();

        foreach ($packages as $package) {
            $offer = $packageOffers->get((int) $package->id);
            $eligibleLists = $this->packagePriceResolver->eligiblePriceListsForPackageAndAgency(
                $package,
                (int) $agency->id,
                (int) $account->id,
            );

            $selectedListId = $offer !== null
                ? (int) $offer->operator_price_list_id
                : (int) ($eligibleLists->first()?->id ?? 0);

            $selectedList = $selectedListId > 0
                ? ($eligibleLists->firstWhere('id', $selectedListId) ?? OperatorPriceList::query()->find($selectedListId))
                : null;

            $agencyPrice = ['has_amount' => false, 'formatted' => '—', 'is_zero' => true];
            if ($selectedList instanceof OperatorPriceList) {
                $agencyPrice = $this->packagePriceResolver->resolvePackageTotal(
                    $package,
                    $selectedList,
                    (int) $agency->id,
                    (int) $account->id,
                );
            }

            $package->setAttribute('offer_status', $offer?->status ?? 'none');
            $package->setAttribute('offer_id', $offer?->id);
            $package->setAttribute('offer_uuid', $offer?->uuid);
            $package->setAttribute('eligible_price_lists', $eligibleLists);
            $package->setAttribute('selected_price_list_id', $selectedListId > 0 ? $selectedListId : null);
            $package->setAttribute('agency_price', $agencyPrice);
            $package->setAttribute('agency_price_is_zero', $this->packagePriceResolver->resolvedAmountIsZero($agencyPrice));
            $package->setAttribute('propose_selectable', $eligibleLists->isNotEmpty() && ! $this->packagePriceResolver->resolvedAmountIsZero($agencyPrice));
            $package->setAttribute('ineligibility_messages', $eligibleLists->isEmpty()
                ? $this->packagePriceResolver->ineligibilityMessages($package, (int) $agency->id, (int) $account->id)
                : []);
        }

        return view('account.package-offers.operator.edit', [
            'account' => $account,
            'agency' => $agency,
            'packages' => $packages,
        ]);
    }

    public function update(
        Request $request,
        Account $agency,
        PackageOfferSyncService $syncService,
    ): RedirectResponse {
        $account = $this->resolveOperatorAccount($request);
        $this->assertAgencyRelationship((int) $account->id, (int) $agency->id);

        $validated = $request->validate([
            'propose' => ['nullable', 'array'],
            'propose.*' => [
                'integer',
                Rule::exists('operator_service_catalog', 'id')->where(
                    fn ($query) => $query->where('operator_id', $account->id)
                ),
            ],
            'price_list' => ['nullable', 'array'],
            'price_list.*' => [
                'integer',
                Rule::exists('operator_price_lists', 'id')->where(
                    fn ($query) => $query->where('operator_id', $account->id)
                ),
            ],
        ]);

        $user = $request->user();
        abort_unless($user !== null, 401);

        $priceListsByPackage = $validated['price_list'] ?? [];
        $proposedMap = [];
        foreach ($validated['propose'] ?? [] as $packageId) {
            $packageId = (int) $packageId;
            $priceListId = (int) ($priceListsByPackage[$packageId] ?? $priceListsByPackage[(string) $packageId] ?? 0);
            if ($packageId > 0 && $priceListId > 0) {
                $proposedMap[$packageId] = $priceListId;
            }
        }

        $packages = OperatorServiceCatalog::query()
            ->where('operator_id', $account->id)
            ->whereIn('id', array_keys($proposedMap))
            ->with(['items.serviceVariant', 'items.serviceOffer'])
            ->get()
            ->keyBy('id');

        $invalidLabels = [];
        foreach ($proposedMap as $packageId => $priceListId) {
            $package = $packages->get($packageId);
            $priceList = OperatorPriceList::query()
                ->where('operator_id', $account->id)
                ->find($priceListId);

            if ($package === null || $priceList === null) {
                continue;
            }

            if (! $this->packagePriceResolver->packageIsOfferableToAgency(
                $package,
                $priceList,
                (int) $agency->id,
                (int) $account->id,
            )) {
                $invalidLabels[] = $package->displayLabel() ?: ('Package #'.$package->id);
            }
        }

        if ($invalidLabels !== []) {
            throw ValidationException::withMessages([
                'propose' => __('account.package_offers.operator_edit_zero_price_validation', [
                    'packages' => implode(', ', $invalidLabels),
                ]),
            ]);
        }

        $syncService->syncProposals(
            (int) $account->id,
            (int) $agency->id,
            $proposedMap,
            $user,
        );

        return redirect()
            ->route('account.package-offers.agencies.edit', ['agency' => $agency])
            ->with('status', __('account.package_offers.operator_status_saved'));
    }

    public function revoke(
        Request $request,
        Account $agency,
        PackageOffer $offer,
        PackageOfferSyncService $syncService,
    ): RedirectResponse {
        $account = $this->resolveOperatorAccount($request);
        $this->assertAgencyRelationship((int) $account->id, (int) $agency->id);

        abort_unless((int) $offer->operator_id === (int) $account->id, 404);
        abort_unless((int) $offer->agency_id === (int) $agency->id, 404);

        $user = $request->user();
        abort_unless($user !== null, 401);

        $syncService->revokePendingProposal(
            (int) $account->id,
            (int) $agency->id,
            (int) $offer->id,
            $user,
        );

        return redirect()
            ->route('account.package-offers.agencies.edit', ['agency' => $agency])
            ->with('status', __('account.package_offers.operator_status_revoked'));
    }

    /**
     * @param  list<int>  $agencyAccountIds
     * @return array<int, array{offered: int, accepted: int}>
     */
    private function offerCountsByAgency(int $operatorAccountId, array $agencyAccountIds): array
    {
        $agencyAccountIds = array_values(array_unique(array_filter(array_map('intval', $agencyAccountIds))));
        if ($agencyAccountIds === []) {
            return [];
        }

        $out = [];
        foreach ($agencyAccountIds as $agencyId) {
            $out[$agencyId] = ['offered' => 0, 'accepted' => 0];
        }

        $rows = PackageOffer::query()
            ->where('operator_id', $operatorAccountId)
            ->whereIn('agency_id', $agencyAccountIds)
            ->whereIn('status', [
                PackageOffer::STATUS_PENDING,
                PackageOffer::STATUS_ACCEPTED,
            ])
            ->selectRaw('agency_id, status, COUNT(*) as package_count')
            ->groupBy('agency_id', 'status')
            ->get();

        foreach ($rows as $row) {
            $agencyId = (int) $row->agency_id;
            $bucket = $row->status === PackageOffer::STATUS_ACCEPTED ? 'accepted' : 'offered';
            if (isset($out[$agencyId])) {
                $out[$agencyId][$bucket] = (int) $row->package_count;
            }
        }

        return $out;
    }

    private function assertAgencyRelationship(int $operatorAccountId, int $agencyAccountId): void
    {
        abort_unless(
            AccountRelationship::query()
                ->where('operator_account_id', $operatorAccountId)
                ->where('provider_account_id', $agencyAccountId)
                ->where('status', AccountRelationship::STATUS_APPROVED)
                ->exists(),
            404
        );
    }

    private function resolveOperatorAccount(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $typeCodes = $account->accountTypes()
            ->where('active', true)
            ->pluck('code');
        abort_unless($typeCodes->contains('operator'), 403);

        return $account;
    }
}
