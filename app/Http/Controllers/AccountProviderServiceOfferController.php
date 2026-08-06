<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountRelationship;
use App\Models\Service;
use App\Models\ServiceOffer;
use App\Models\ServiceVariant;
use App\Services\OperatorVariantPriceResolver;
use App\Services\ServiceOfferSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class AccountProviderServiceOfferController extends Controller
{
    public function operatorsIndex(Request $request): View
    {
        $account = $this->resolveProviderAccount($request);

        $relationships = AccountRelationship::query()
            ->where('provider_account_id', $account->id)
            ->where('status', AccountRelationship::STATUS_APPROVED)
            ->with('operatorAccount')
            ->orderBy('id')
            ->get();

        $offerCountsByOperator = $this->offerCountsByOperator(
            (int) $account->id,
            $relationships->pluck('operator_account_id')->map(fn ($id) => (int) $id)->all(),
        );

        foreach ($relationships as $relationship) {
            $operatorId = (int) $relationship->operator_account_id;
            $offered = $offerCountsByOperator[$operatorId]['offered'] ?? ['services' => 0, 'variants' => 0];
            $accepted = $offerCountsByOperator[$operatorId]['accepted'] ?? ['services' => 0, 'variants' => 0];
            $relationship->setAttribute('offered_service_count', $offered['services']);
            $relationship->setAttribute('offered_variant_count', $offered['variants']);
            $relationship->setAttribute('accepted_service_count', $accepted['services']);
            $relationship->setAttribute('accepted_variant_count', $accepted['variants']);
        }

        return view('account.service-offers.provider.operators', [
            'account' => $account,
            'relationships' => $relationships,
        ]);
    }

    public function edit(Request $request, Account $operator, OperatorVariantPriceResolver $priceResolver): View
    {
        $account = $this->resolveProviderAccount($request);
        abort_unless(
            AccountRelationship::query()
                ->where('provider_account_id', $account->id)
                ->where('operator_account_id', $operator->id)
                ->where('status', AccountRelationship::STATUS_APPROVED)
                ->exists(),
            404
        );

        $variantOffers = ServiceOffer::query()
            ->where('provider_id', $account->id)
            ->where('operator_id', $operator->id)
            ->get()
            ->keyBy(fn (ServiceOffer $o) => (int) $o->service_variant_id);

        $serviceStatusOptions = $this->eligibleServiceStatusesForOperatorOffers($account);
        $serviceStatusFilter = $this->resolveServiceStatusFilterForList(
            (string) $request->query('service_status', ''),
            $serviceStatusOptions,
        );

        $services = Service::query()
            ->where('account_id', $account->id)
            ->withVariants()
            ->whereNotIn('status', Service::catalogStatusesOmittedFromOperatorOffers())
            ->when($serviceStatusFilter !== '', fn ($q) => $q->where('status', $serviceStatusFilter))
            ->with([
                'translations',
                'serviceVariants' => fn ($q) => $q
                    ->whereNotIn('status', ServiceVariant::catalogStatusesOmittedFromOperatorOffers())
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->with([
                        'translations.language.locale',
                        'currency.lmpCurrency',
                    ]),
            ])
            ->orderBy('id')
            ->get();

        $activeAssignment = $priceResolver->activeAssignment((int) $account->id, (int) $operator->id);
        $operatorPriceListName = $activeAssignment?->priceList?->name;
        $operatorHasPriceList = $activeAssignment !== null;
        $operatorPriceListValidity = $activeAssignment !== null
            ? locale_date_range($activeAssignment->valid_from, $activeAssignment->valid_to)
            : null;

        foreach ($services as $service) {
            foreach ($service->serviceVariants as $variant) {
                $offer = $variantOffers->get((int) $variant->id);
                $variant->setAttribute('offer_status', $offer?->status ?? 'none');
                $variant->setAttribute('offer_id', $offer?->id);
                $variant->setAttribute('offer_uuid', $offer?->uuid);
                $operatorPrice = $priceResolver->resolve($variant, (int) $account->id, (int) $operator->id);
                $variant->setAttribute('operator_price', $operatorPrice);
                $variant->setAttribute('operator_price_is_zero', $priceResolver->resolvedAmountIsZero($operatorPrice));
                $variant->setAttribute('operator_has_price_list', $operatorHasPriceList);
                $variant->setAttribute('operator_price_list_name', $operatorPriceListName);
                $variant->setAttribute('operator_price_list_validity', $operatorPriceListValidity);
            }
        }

        return view('account.service-offers.provider.edit', [
            'account' => $account,
            'operator' => $operator,
            'services' => $services,
            'serviceStatusOptions' => $serviceStatusOptions,
            'serviceStatusFilter' => $serviceStatusFilter,
            'operatorHasPriceList' => $operatorHasPriceList,
            'operatorPriceListName' => $operatorPriceListName,
            'operatorPriceListValidity' => $operatorPriceListValidity,
        ]);
    }

    public function update(
        Request $request,
        Account $operator,
        ServiceOfferSyncService $syncService,
        OperatorVariantPriceResolver $priceResolver,
    ): RedirectResponse {
        $account = $this->resolveProviderAccount($request);
        abort_unless(
            AccountRelationship::query()
                ->where('provider_account_id', $account->id)
                ->where('operator_account_id', $operator->id)
                ->where('status', AccountRelationship::STATUS_APPROVED)
                ->exists(),
            404
        );

        $allowedStatusesCollection = $this->eligibleServiceStatusesForOperatorOffers($account);
        $allowedServiceStatuses = $allowedStatusesCollection->all();

        $serviceStatusRules = ['nullable', 'string'];
        if ($allowedServiceStatuses !== []) {
            $serviceStatusRules[] = Rule::in($allowedServiceStatuses);
        }

        $omittedServiceStatuses = Service::catalogStatusesOmittedFromOperatorOffers();

        $validated = $request->validate([
            'propose' => ['nullable', 'array'],
            'propose.*' => [
                'integer',
                'distinct',
                Rule::exists('service_variants', 'id')->where(function ($query) use ($account, $omittedServiceStatuses): void {
                    $query->where('status', 'active')
                        ->whereIn(
                            'service_id',
                            Service::query()
                                ->where('account_id', $account->id)
                                ->where('status', 'active')
                                ->whereNotIn('status', $omittedServiceStatuses)
                                ->select('id')
                        );
                }),
            ],
            'service_status' => $serviceStatusRules,
        ]);

        $user = $request->user();
        abort_unless($user !== null, 401);

        $proposedVariantIds = array_values(array_unique(array_map('intval', $validated['propose'] ?? [])));
        if ($proposedVariantIds !== []) {
            $variants = ServiceVariant::query()
                ->whereIn('id', $proposedVariantIds)
                ->with('currency.lmpCurrency')
                ->get()
                ->keyBy('id');

            $zeroPriceSkus = [];
            foreach ($proposedVariantIds as $variantId) {
                $variant = $variants->get($variantId);
                if ($variant === null) {
                    continue;
                }
                $resolved = $priceResolver->resolve($variant, (int) $account->id, (int) $operator->id);
                if ($priceResolver->resolvedAmountIsZero($resolved)) {
                    $zeroPriceSkus[] = $variant->sku;
                }
            }

            if ($zeroPriceSkus !== []) {
                throw ValidationException::withMessages([
                    'propose' => __('account.service_offers.provider_edit_zero_price_validation', [
                        'variants' => implode(', ', $zeroPriceSkus),
                    ]),
                ]);
            }
        }

        $syncService->syncProposals(
            (int) $account->id,
            (int) $operator->id,
            $proposedVariantIds,
            $user,
        );

        $filterForRedirect = $this->resolveServiceStatusFilterForList(
            (string) ($validated['service_status'] ?? ''),
            $allowedStatusesCollection,
        );
        $query = $filterForRedirect !== '' ? ['service_status' => $filterForRedirect] : [];

        return redirect()
            ->route('account.service-offers.operators.edit', array_merge(['operator' => $operator], $query))
            ->with('status', __('account.service_offers.provider_status_saved'));
    }

    public function revoke(
        Request $request,
        Account $operator,
        ServiceOffer $offer,
        ServiceOfferSyncService $syncService,
    ): RedirectResponse {
        $account = $this->resolveProviderAccount($request);
        abort_unless(
            AccountRelationship::query()
                ->where('provider_account_id', $account->id)
                ->where('operator_account_id', $operator->id)
                ->where('status', AccountRelationship::STATUS_APPROVED)
                ->exists(),
            404
        );

        $user = $request->user();
        abort_unless($user !== null, 401);

        $syncService->revokePendingProposal(
            (int) $account->id,
            (int) $operator->id,
            (int) $offer->id,
            $user,
        );

        $serviceStatusFilter = $this->resolveServiceStatusFilterForList(
            (string) ($request->input('service_status') ?? $request->query('service_status', '')),
            $this->eligibleServiceStatusesForOperatorOffers($account),
        );
        $query = $serviceStatusFilter !== '' ? ['service_status' => $serviceStatusFilter] : [];

        return redirect()
            ->route('account.service-offers.operators.edit', array_merge(['operator' => $operator], $query))
            ->with('status', __('account.service_offers.provider_status_revoked'));
    }

    /**
     * Distinct service.status values in the operator-offers picker (services with variants only).
     *
     * @return Collection<int, string>
     */
    private function eligibleServiceStatusesForOperatorOffers(Account $account): Collection
    {
        $omittedServiceStatuses = Service::catalogStatusesOmittedFromOperatorOffers();
        $omittedVariantStatuses = ServiceVariant::catalogStatusesOmittedFromOperatorOffers();

        return Service::query()
            ->where('account_id', $account->id)
            ->withVariants()
            ->whereNotIn('status', $omittedServiceStatuses)
            ->whereHas(
                'serviceVariants',
                fn ($vq) => $vq->whereNotIn('status', $omittedVariantStatuses)
            )
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->values();
    }

    private function resolveServiceStatusFilterForList(string $raw, Collection $allowedStatuses): string
    {
        $raw = trim($raw);
        if ($raw === '' || ! $allowedStatuses->contains($raw)) {
            return '';
        }

        return $raw;
    }

    /**
     * Pending and accepted variant offers per operator.
     *
     * @param  list<int>  $operatorAccountIds
     * @return array<int, array{
     *     offered: array{services: int, variants: int},
     *     accepted: array{services: int, variants: int}
     * }>
     */
    private function offerCountsByOperator(int $providerAccountId, array $operatorAccountIds): array
    {
        $operatorAccountIds = array_values(array_unique(array_filter(array_map('intval', $operatorAccountIds))));
        if ($operatorAccountIds === []) {
            return [];
        }

        $emptyBucket = fn (): array => ['services' => 0, 'variants' => 0];
        $out = [];
        foreach ($operatorAccountIds as $operatorId) {
            $out[$operatorId] = [
                'offered' => $emptyBucket(),
                'accepted' => $emptyBucket(),
            ];
        }

        $rows = ServiceOffer::query()
            ->join('service_variants', 'service_offers.service_variant_id', '=', 'service_variants.id')
            ->where('service_offers.provider_id', $providerAccountId)
            ->whereIn('service_offers.operator_id', $operatorAccountIds)
            ->whereIn('service_offers.status', [
                ServiceOffer::STATUS_PENDING,
                ServiceOffer::STATUS_ACCEPTED,
            ])
            ->selectRaw('service_offers.operator_id, service_offers.status, COUNT(*) as variant_count, COUNT(DISTINCT service_variants.service_id) as service_count')
            ->groupBy('service_offers.operator_id', 'service_offers.status')
            ->get();

        foreach ($rows as $row) {
            $operatorId = (int) $row->operator_id;
            $bucket = $row->status === ServiceOffer::STATUS_ACCEPTED ? 'accepted' : 'offered';
            if (! isset($out[$operatorId])) {
                continue;
            }
            $out[$operatorId][$bucket] = [
                'services' => (int) $row->service_count,
                'variants' => (int) $row->variant_count,
            ];
        }

        return $out;
    }

    private function resolveProviderAccount(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $typeCodes = $account->accountTypes()
            ->where('active', true)
            ->pluck('code');
        abort_unless($typeCodes->contains('provider'), 403);

        return $account;
    }
}
