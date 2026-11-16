<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProviderServiceOffer;
use App\Models\ProviderSupplyRelationship;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\ServiceVariant;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Supplier proposes its own non-combo variants to a buyer provider.
 * Accepted offers stay until the supplier changes availability.
 */
final class ProviderSupplyOfferSyncService
{
    public function __construct(
        private readonly AccountNotificationService $accountNotifications,
    ) {
    }

    /**
     * @param  list<int>  $proposedVariantIds
     */
    public function syncProposals(int $supplierAccountId, int $buyerAccountId, array $proposedVariantIds, User $actingUser): int
    {
        $this->assertApprovedLink($supplierAccountId, $buyerAccountId);

        $proposedVariantIds = array_values(array_unique(array_map('intval', $proposedVariantIds)));
        $allowedVariantIds = $this->allowedVariantIds($supplierAccountId);

        foreach ($proposedVariantIds as $variantId) {
            if (! in_array($variantId, $allowedVariantIds, true)) {
                throw ValidationException::withMessages([
                    'propose' => __('account.provider_supply.validation.variant_not_allowed'),
                ]);
            }
        }

        $newPendingCount = 0;

        DB::transaction(function () use ($supplierAccountId, $buyerAccountId, $proposedVariantIds, $allowedVariantIds, &$newPendingCount): void {
            $newPendingCount = $this->syncInTransaction(
                $supplierAccountId,
                $buyerAccountId,
                $proposedVariantIds,
                $allowedVariantIds,
            );
        });

        if ($newPendingCount > 0) {
            $this->accountNotifications->createForAccount(
                accountId: $buyerAccountId,
                type: 'provider_supply_offer_received',
                title: (string) __('account.provider_supply.notification_offer_title'),
                message: (string) __('account.provider_supply.notification_offer_message', [
                    'count' => $newPendingCount,
                ]),
                recipientUserId: null,
                data: [
                    'supplier_account_id' => $supplierAccountId,
                    'new_pending_count' => $newPendingCount,
                    'created_by_user_id' => (int) $actingUser->id,
                ],
            );
        }

        return $newPendingCount;
    }

    public function updateAvailability(
        int $supplierAccountId,
        int $buyerAccountId,
        ProviderServiceOffer $offer,
        string $availability,
        User $actingUser,
    ): void {
        $this->assertApprovedLink($supplierAccountId, $buyerAccountId);

        if ((int) $offer->supplier_id !== $supplierAccountId || (int) $offer->buyer_id !== $buyerAccountId) {
            abort(404);
        }

        if ($offer->status !== ProviderServiceOffer::STATUS_ACCEPTED) {
            throw ValidationException::withMessages([
                'availability' => __('account.provider_supply.validation.availability_requires_accepted'),
            ]);
        }

        if (! in_array($availability, [
            ProviderServiceOffer::AVAILABILITY_ACTIVE,
            ProviderServiceOffer::AVAILABILITY_SUSPENDED,
            ProviderServiceOffer::AVAILABILITY_DISCONTINUED,
        ], true)) {
            throw ValidationException::withMessages([
                'availability' => __('account.provider_supply.validation.availability_invalid'),
            ]);
        }

        $wasUsable = $offer->isUsableInCombo();

        $offer->update([
            'availability' => $availability,
            'withdrawn_at' => $availability === ProviderServiceOffer::AVAILABILITY_ACTIVE ? null : now(),
        ]);

        if ($wasUsable && ! $offer->isUsableInCombo()) {
            $this->accountNotifications->createForAccount(
                accountId: $buyerAccountId,
                type: 'provider_supply_offer_unavailable',
                title: (string) __('account.provider_supply.notification_unavailable_title'),
                message: (string) __('account.provider_supply.notification_unavailable_message'),
                recipientUserId: null,
                data: [
                    'supplier_account_id' => $supplierAccountId,
                    'provider_service_offer_id' => (int) $offer->id,
                    'availability' => $availability,
                    'created_by_user_id' => (int) $actingUser->id,
                ],
            );
        }
    }

    private function assertApprovedLink(int $supplierAccountId, int $buyerAccountId): void
    {
        $linked = ProviderSupplyRelationship::query()
            ->where('supplier_account_id', $supplierAccountId)
            ->where('buyer_account_id', $buyerAccountId)
            ->where('status', ProviderSupplyRelationship::STATUS_APPROVED)
            ->exists();

        abort_unless($linked, 403);
    }

    /**
     * @return list<int>
     */
    private function allowedVariantIds(int $supplierAccountId): array
    {
        return ServiceVariant::query()
            ->whereHas('service', function ($query) use ($supplierAccountId): void {
                $query->where('account_id', $supplierAccountId)
                    ->whereHas('serviceType', fn ($typeQuery) => $typeQuery->where('code', '!=', ServiceType::CODE_COMBO));
            })
            ->whereNotIn('status', ServiceVariant::catalogStatusesOmittedFromOperatorOffers())
            ->whereHas('service', fn ($query) => $query->whereNotIn('status', Service::catalogStatusesOmittedFromOperatorOffers()))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * @param  list<int>  $proposedVariantIds
     * @param  list<int>  $allowedVariantIds
     */
    private function syncInTransaction(
        int $supplierAccountId,
        int $buyerAccountId,
        array $proposedVariantIds,
        array $allowedVariantIds,
    ): int {
        $offers = ProviderServiceOffer::query()
            ->where('supplier_id', $supplierAccountId)
            ->where('buyer_id', $buyerAccountId)
            ->whereIn('service_variant_id', $allowedVariantIds)
            ->get()
            ->keyBy(fn (ProviderServiceOffer $offer) => (int) $offer->service_variant_id);

        $newPendingCount = 0;

        foreach ($allowedVariantIds as $variantId) {
            $want = in_array($variantId, $proposedVariantIds, true);
            /** @var ProviderServiceOffer|null $offer */
            $offer = $offers->get($variantId);

            if ($want) {
                if ($offer === null) {
                    ProviderServiceOffer::query()->create([
                        'supplier_id' => $supplierAccountId,
                        'buyer_id' => $buyerAccountId,
                        'service_variant_id' => $variantId,
                        'status' => ProviderServiceOffer::STATUS_PENDING,
                        'availability' => ProviderServiceOffer::AVAILABILITY_ACTIVE,
                        'offered_at' => now(),
                    ]);
                    $newPendingCount++;
                } elseif ($offer->status === ProviderServiceOffer::STATUS_REJECTED) {
                    $offer->update([
                        'status' => ProviderServiceOffer::STATUS_PENDING,
                        'availability' => ProviderServiceOffer::AVAILABILITY_ACTIVE,
                        'offered_at' => now(),
                        'withdrawn_at' => null,
                    ]);
                    $newPendingCount++;
                }
            } elseif ($offer !== null && in_array($offer->status, [
                ProviderServiceOffer::STATUS_PENDING,
                ProviderServiceOffer::STATUS_REJECTED,
            ], true)) {
                $offer->delete();
            }
        }

        return $newPendingCount;
    }

    /**
     * Variants the supplier may still propose, for the offer screen.
     *
     * @return Collection<int, Service>
     */
    public function catalogForSupplier(int $supplierAccountId): Collection
    {
        return Service::query()
            ->where('account_id', $supplierAccountId)
            ->whereNotIn('status', Service::catalogStatusesOmittedFromOperatorOffers())
            ->whereHas('serviceType', fn ($query) => $query->where('code', '!=', ServiceType::CODE_COMBO))
            ->with([
                'translations.language.locale',
                'serviceVariants' => fn ($query) => $query
                    ->whereNotIn('status', ServiceVariant::catalogStatusesOmittedFromOperatorOffers())
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->with('translations.language.locale'),
            ])
            ->orderBy('id')
            ->get()
            ->filter(fn (Service $service) => $service->serviceVariants->isNotEmpty())
            ->values();
    }
}
