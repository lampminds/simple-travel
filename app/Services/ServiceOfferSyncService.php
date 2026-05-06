<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountRelationship;
use App\Models\ServiceOffer;
use App\Models\ServiceVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Syncs variant-level service offers from a provider to an operator (pending until accepted).
 */
final class ServiceOfferSyncService
{
    public function __construct(
        private readonly AccountNotificationService $accountNotifications,
    ) {}

    /**
     * @param  list<int>  $proposedVariantIds  Variants the provider marks for proposal (checkboxes); accepted variants are merged server-side.
     * @return array{new_pending_count: int}
     */
    public function syncVariantProposals(
        int $providerAccountId,
        int $operatorAccountId,
        array $proposedVariantIds,
        User $actingUser,
    ): array {
        abort_unless(
            AccountRelationship::query()
                ->where('provider_account_id', $providerAccountId)
                ->where('operator_account_id', $operatorAccountId)
                ->where('status', AccountRelationship::STATUS_APPROVED)
                ->exists(),
            403
        );

        $proposedVariantIds = array_values(array_unique(array_map('intval', $proposedVariantIds)));

        $acceptedVariantIds = ServiceOffer::query()
            ->where('provider_id', $providerAccountId)
            ->where('operator_id', $operatorAccountId)
            ->whereNotNull('service_variant_id')
            ->where('status', ServiceOffer::STATUS_ACCEPTED)
            ->pluck('service_variant_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $wantVariantIds = collect($proposedVariantIds)
            ->merge($acceptedVariantIds)
            ->unique()
            ->values()
            ->all();

        $allowedVariantIds = ServiceVariant::query()
            ->whereHas('service', fn ($q) => $q->where('account_id', $providerAccountId))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($wantVariantIds as $vid) {
            abort_unless(in_array($vid, $allowedVariantIds, true), 422);
        }

        $newPendingCount = 0;

        DB::transaction(function () use (
            $providerAccountId,
            $operatorAccountId,
            $wantVariantIds,
            $allowedVariantIds,
            &$newPendingCount,
        ): void {
            $offers = ServiceOffer::query()
                ->where('provider_id', $providerAccountId)
                ->where('operator_id', $operatorAccountId)
                ->whereNotNull('service_variant_id')
                ->whereIn('service_variant_id', $allowedVariantIds)
                ->get()
                ->keyBy(fn (ServiceOffer $o) => (int) $o->service_variant_id);

            foreach ($allowedVariantIds as $variantId) {
                $want = in_array($variantId, $wantVariantIds, true);
                /** @var ServiceOffer|null $offer */
                $offer = $offers->get($variantId);

                if ($want) {
                    if ($offer === null) {
                        ServiceOffer::query()->create([
                            'provider_id' => $providerAccountId,
                            'operator_id' => $operatorAccountId,
                            'service_id' => null,
                            'service_variant_id' => $variantId,
                            'status' => ServiceOffer::STATUS_PENDING,
                            'availability' => ServiceOffer::AVAILABILITY_ACTIVE,
                            'offered_at' => now(),
                        ]);
                        $newPendingCount++;
                    } elseif ($offer->status === ServiceOffer::STATUS_REJECTED) {
                        $offer->update([
                            'status' => ServiceOffer::STATUS_PENDING,
                            'availability' => ServiceOffer::AVAILABILITY_ACTIVE,
                            'offered_at' => now(),
                        ]);
                        $newPendingCount++;
                    }
                } elseif ($offer !== null && in_array($offer->status, [
                    ServiceOffer::STATUS_PENDING,
                    ServiceOffer::STATUS_REJECTED,
                ], true)) {
                    $offer->delete();
                }
            }
        });

        if ($newPendingCount > 0) {
            $this->notifyOperatorOfNewPending(
                $providerAccountId,
                $operatorAccountId,
                $newPendingCount,
                $actingUser
            );
        }

        return ['new_pending_count' => $newPendingCount];
    }

    private function notifyOperatorOfNewPending(
        int $providerAccountId,
        int $operatorAccountId,
        int $newPendingCount,
        User $actingUser,
    ): void {
        $provider = Account::query()->find($providerAccountId);
        $providerLabel = $provider?->commercial_name ?? $provider?->name ?? $provider?->nick ?? (string) $providerAccountId;

        $url = route('account.service-offers.index', ['as' => 'operator'], true);

        $this->accountNotifications->createForAccount(
            accountId: $operatorAccountId,
            type: 'service_offer_pending',
            title: (string) __('account.service_offers.notification_pending_title', ['provider' => $providerLabel]),
            message: (string) __('account.service_offers.notification_pending_message', [
                'provider' => $providerLabel,
                'count' => $newPendingCount,
                'url' => $url,
            ]),
            recipientUserId: null,
            data: [
                'provider_account_id' => $providerAccountId,
                'new_pending_count' => $newPendingCount,
                'created_by_user_id' => $actingUser->id,
                'created_by_user_name' => $actingUser->name,
            ],
        );
    }
}
