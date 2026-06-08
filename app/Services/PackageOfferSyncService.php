<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\AccountRelationship;
use App\Models\OperatorPriceList;
use App\Models\OperatorServiceCatalog;
use App\Models\PackageOffer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Syncs package-level offers from an operator to an agency (pending until accepted).
 */
final class PackageOfferSyncService
{
    public function __construct(
        private readonly AccountNotificationService $accountNotifications,
        private readonly OperatorPackageAgencyPriceResolver $packagePriceResolver,
    ) {}

    /**
     * @param  array<int, int>  $proposedPackagePriceLists  package_id => price_list_id
     * @return array{new_pending_count: int}
     */
    public function syncProposals(
        int $operatorAccountId,
        int $agencyAccountId,
        array $proposedPackagePriceLists,
        User $actingUser,
    ): array {
        $this->assertApprovedRelationship($operatorAccountId, $agencyAccountId);

        $proposedPackagePriceLists = $this->normalizeProposedMap($proposedPackagePriceLists);
        $this->assertProposalsArePriced($operatorAccountId, $agencyAccountId, $proposedPackagePriceLists);

        $newPendingCount = 0;

        DB::transaction(function () use (
            $operatorAccountId,
            $agencyAccountId,
            $proposedPackagePriceLists,
            &$newPendingCount,
        ): void {
            $newPendingCount = $this->syncOffersInTransaction(
                $operatorAccountId,
                $agencyAccountId,
                $proposedPackagePriceLists,
            );
        });

        if ($newPendingCount > 0) {
            $this->notifyAgencyOfNewPending(
                $operatorAccountId,
                $agencyAccountId,
                $newPendingCount,
                $actingUser,
            );
        }

        return ['new_pending_count' => $newPendingCount];
    }

    public function revokePendingProposal(
        int $operatorAccountId,
        int $agencyAccountId,
        int $packageOfferId,
        User $actingUser,
    ): void {
        $this->assertApprovedRelationship($operatorAccountId, $agencyAccountId);

        $offer = PackageOffer::query()
            ->where('id', $packageOfferId)
            ->where('operator_id', $operatorAccountId)
            ->where('agency_id', $agencyAccountId)
            ->where('status', PackageOffer::STATUS_PENDING)
            ->with(['catalog.translations'])
            ->first();

        abort_unless($offer !== null, 404);

        $packageLabel = $this->packageLabel($offer->catalog);

        DB::transaction(function () use ($offer): void {
            $offer->update(['withdrawn_at' => now()]);
            $offer->delete();
        });

        $this->notifyAgencyOfRevokedProposal(
            $operatorAccountId,
            $agencyAccountId,
            $packageLabel,
            $actingUser,
        );
    }

    public function notifyOperatorOfAcceptedOffer(PackageOffer $offer, User $actingUser): void
    {
        $offer->loadMissing([
            'catalog.translations',
            'agencyAccount',
        ]);

        $operatorAccountId = (int) $offer->operator_id;
        $agencyAccountId = (int) $offer->agency_id;
        $agency = $offer->agencyAccount ?? Account::query()->find($agencyAccountId);
        $agencyLabel = $agency?->commercial_name ?? $agency?->name ?? $agency?->nick ?? (string) $agencyAccountId;
        $packageLabel = $this->packageLabel($offer->catalog);

        $url = $agency instanceof Account
            ? route('account.package-offers.agencies.edit', ['agency' => $agency], true)
            : route('account.package-offers.index', ['as' => 'operator'], true);

        $this->accountNotifications->createForAccount(
            accountId: $operatorAccountId,
            type: 'package_offer_accepted',
            title: (string) __('account.package_offers.notification_accepted_title', ['agency' => $agencyLabel]),
            message: (string) __('account.package_offers.notification_accepted_message', [
                'agency' => $agencyLabel,
                'package' => $packageLabel,
                'url' => $url,
            ]),
            recipientUserId: null,
            data: [
                'agency_account_id' => $agencyAccountId,
                'package_offer_id' => (int) $offer->id,
                'package_label' => $packageLabel,
                'created_by_user_id' => $actingUser->id,
                'created_by_user_name' => $actingUser->name,
            ],
        );
    }

    /**
     * @param  array<int, int>  $proposedPackagePriceLists
     */
    private function syncOffersInTransaction(
        int $operatorAccountId,
        int $agencyAccountId,
        array $proposedPackagePriceLists,
    ): int {
        $eligiblePackageIds = OperatorServiceCatalog::query()
            ->where('operator_id', $operatorAccountId)
            ->where('status', 'active')
            ->whereHas('items', fn ($q) => $q->where('inclusion_mode', 'included'))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $acceptedPackageIds = PackageOffer::query()
            ->where('operator_id', $operatorAccountId)
            ->where('agency_id', $agencyAccountId)
            ->where('status', PackageOffer::STATUS_ACCEPTED)
            ->pluck('operator_service_catalog_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $pendingPackageIds = PackageOffer::query()
            ->where('operator_id', $operatorAccountId)
            ->where('agency_id', $agencyAccountId)
            ->where('status', PackageOffer::STATUS_PENDING)
            ->pluck('operator_service_catalog_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $wantMap = $proposedPackagePriceLists;
        foreach ($acceptedPackageIds as $packageId) {
            if (! array_key_exists($packageId, $wantMap)) {
                $existing = PackageOffer::query()
                    ->where('operator_id', $operatorAccountId)
                    ->where('agency_id', $agencyAccountId)
                    ->where('operator_service_catalog_id', $packageId)
                    ->where('status', PackageOffer::STATUS_ACCEPTED)
                    ->first();
                if ($existing !== null) {
                    $wantMap[$packageId] = (int) $existing->operator_price_list_id;
                }
            }
        }
        foreach ($pendingPackageIds as $packageId) {
            if (! array_key_exists($packageId, $wantMap)) {
                $existing = PackageOffer::query()
                    ->where('operator_id', $operatorAccountId)
                    ->where('agency_id', $agencyAccountId)
                    ->where('operator_service_catalog_id', $packageId)
                    ->where('status', PackageOffer::STATUS_PENDING)
                    ->first();
                if ($existing !== null) {
                    $wantMap[$packageId] = (int) $existing->operator_price_list_id;
                }
            }
        }

        PackageOffer::query()
            ->where('operator_id', $operatorAccountId)
            ->where('agency_id', $agencyAccountId)
            ->whereIn('status', [
                PackageOffer::STATUS_PENDING,
                PackageOffer::STATUS_REJECTED,
            ])
            ->whereNotIn('operator_service_catalog_id', $eligiblePackageIds)
            ->delete();

        $offers = PackageOffer::query()
            ->where('operator_id', $operatorAccountId)
            ->where('agency_id', $agencyAccountId)
            ->whereIn('operator_service_catalog_id', $eligiblePackageIds)
            ->get()
            ->keyBy(fn (PackageOffer $offer) => (int) $offer->operator_service_catalog_id);

        $newPendingCount = 0;

        foreach ($eligiblePackageIds as $packageId) {
            $want = array_key_exists($packageId, $wantMap);
            /** @var PackageOffer|null $offer */
            $offer = $offers->get($packageId);

            if ($want) {
                $priceListId = (int) $wantMap[$packageId];

                if ($offer === null) {
                    PackageOffer::query()->create([
                        'operator_id' => $operatorAccountId,
                        'agency_id' => $agencyAccountId,
                        'operator_service_catalog_id' => $packageId,
                        'operator_price_list_id' => $priceListId,
                        'status' => PackageOffer::STATUS_PENDING,
                        'availability' => PackageOffer::AVAILABILITY_ACTIVE,
                        'offered_at' => now(),
                    ]);
                    $newPendingCount++;
                } elseif ($offer->status === PackageOffer::STATUS_REJECTED) {
                    $offer->update([
                        'operator_price_list_id' => $priceListId,
                        'status' => PackageOffer::STATUS_PENDING,
                        'availability' => PackageOffer::AVAILABILITY_ACTIVE,
                        'offered_at' => now(),
                    ]);
                    $newPendingCount++;
                } elseif ($offer->status === PackageOffer::STATUS_PENDING) {
                    if ((int) $offer->operator_price_list_id !== $priceListId) {
                        $offer->update(['operator_price_list_id' => $priceListId]);
                    }
                }
            } elseif ($offer !== null && $offer->status === PackageOffer::STATUS_REJECTED) {
                $offer->delete();
            }
        }

        return $newPendingCount;
    }

    /**
     * @param  array<int, int|string|null>  $proposedPackagePriceLists
     * @return array<int, int>
     */
    private function normalizeProposedMap(array $proposedPackagePriceLists): array
    {
        $out = [];
        foreach ($proposedPackagePriceLists as $packageId => $priceListId) {
            $packageId = (int) $packageId;
            $priceListId = (int) $priceListId;
            if ($packageId > 0 && $priceListId > 0) {
                $out[$packageId] = $priceListId;
            }
        }

        return $out;
    }

    /**
     * @param  array<int, int>  $proposedPackagePriceLists
     */
    private function assertProposalsArePriced(
        int $operatorAccountId,
        int $agencyAccountId,
        array $proposedPackagePriceLists,
    ): void {
        if ($proposedPackagePriceLists === []) {
            return;
        }

        $packages = OperatorServiceCatalog::query()
            ->where('operator_id', $operatorAccountId)
            ->whereIn('id', array_keys($proposedPackagePriceLists))
            ->with(['items.serviceVariant', 'items.serviceOffer'])
            ->get()
            ->keyBy('id');

        $priceLists = OperatorPriceList::query()
            ->where('operator_id', $operatorAccountId)
            ->whereIn('id', array_values($proposedPackagePriceLists))
            ->with('currency')
            ->get()
            ->keyBy('id');

        $invalidLabels = [];

        foreach ($proposedPackagePriceLists as $packageId => $priceListId) {
            $package = $packages->get($packageId);
            $priceList = $priceLists->get($priceListId);

            if ($package === null || $priceList === null) {
                abort(422);
            }

            if (! $this->packagePriceResolver->packageIsOfferableToAgency(
                $package,
                $priceList,
                $agencyAccountId,
                $operatorAccountId,
            )) {
                $invalidLabels[] = $this->packageLabel($package);
            }
        }

        if ($invalidLabels !== []) {
            abort(422, (string) __('account.package_offers.operator_edit_zero_price_validation', [
                'packages' => implode(', ', $invalidLabels),
            ]));
        }
    }

    private function assertApprovedRelationship(int $operatorAccountId, int $agencyAccountId): void
    {
        abort_unless(
            AccountRelationship::query()
                ->where('operator_account_id', $operatorAccountId)
                ->where('provider_account_id', $agencyAccountId)
                ->where('status', AccountRelationship::STATUS_APPROVED)
                ->exists(),
            403
        );
    }

    private function packageLabel(?OperatorServiceCatalog $package): string
    {
        if ($package === null) {
            return '—';
        }

        $label = $package->displayLabel();

        return $label !== '' ? $label : ('Package #'.$package->id);
    }

    private function notifyAgencyOfNewPending(
        int $operatorAccountId,
        int $agencyAccountId,
        int $newPendingCount,
        User $actingUser,
    ): void {
        $operator = Account::query()->find($operatorAccountId);
        $operatorLabel = $operator?->commercial_name ?? $operator?->name ?? $operator?->nick ?? (string) $operatorAccountId;

        $url = route('account.package-offers.index', ['as' => 'agency'], true);

        $this->accountNotifications->createForAccount(
            accountId: $agencyAccountId,
            type: 'package_offer_pending',
            title: (string) __('account.package_offers.notification_pending_title', ['operator' => $operatorLabel]),
            message: (string) __('account.package_offers.notification_pending_message', [
                'operator' => $operatorLabel,
                'count' => $newPendingCount,
                'url' => $url,
            ]),
            recipientUserId: null,
            data: [
                'operator_account_id' => $operatorAccountId,
                'new_pending_count' => $newPendingCount,
                'created_by_user_id' => $actingUser->id,
                'created_by_user_name' => $actingUser->name,
            ],
        );
    }

    private function notifyAgencyOfRevokedProposal(
        int $operatorAccountId,
        int $agencyAccountId,
        string $packageLabel,
        User $actingUser,
    ): void {
        $operator = Account::query()->find($operatorAccountId);
        $operatorLabel = $operator?->commercial_name ?? $operator?->name ?? $operator?->nick ?? (string) $operatorAccountId;

        $url = route('account.package-offers.index', ['as' => 'agency'], true);

        $this->accountNotifications->createForAccount(
            accountId: $agencyAccountId,
            type: 'package_offer_revoked',
            title: (string) __('account.package_offers.notification_revoked_title', ['operator' => $operatorLabel]),
            message: (string) __('account.package_offers.notification_revoked_message', [
                'operator' => $operatorLabel,
                'package' => $packageLabel,
                'url' => $url,
            ]),
            recipientUserId: null,
            data: [
                'operator_account_id' => $operatorAccountId,
                'package_label' => $packageLabel,
                'created_by_user_id' => $actingUser->id,
                'created_by_user_name' => $actingUser->name,
            ],
        );
    }
}
