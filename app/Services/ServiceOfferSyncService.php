<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\AccountRelationship;
use App\Models\Service;
use App\Models\ServiceOffer;
use App\Models\ServiceVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Syncs whole-service and variant-level offers from a provider to an operator (pending until accepted).
 */
final class ServiceOfferSyncService
{
    public function __construct(
        private readonly AccountNotificationService $accountNotifications,
    ) {}

    /**
     * @param  list<int>  $proposedVariantIds
     * @param  list<int>  $proposedServiceIds
     * @return array{new_pending_count: int}
     */
    public function syncProposals(
        int $providerAccountId,
        int $operatorAccountId,
        array $proposedVariantIds,
        array $proposedServiceIds,
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
        $proposedServiceIds = array_values(array_unique(array_map('intval', $proposedServiceIds)));

        $omittedServiceStatuses = Service::catalogStatusesOmittedFromOperatorOffers();
        $omittedVariantStatuses = ServiceVariant::catalogStatusesOmittedFromOperatorOffers();

        $allowedServiceIds = Service::query()
            ->where('account_id', $providerAccountId)
            ->whereNotIn('status', $omittedServiceStatuses)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $allowedVariantIds = ServiceVariant::query()
            ->whereHas('service', function ($q) use ($providerAccountId, $omittedServiceStatuses): void {
                $q->where('account_id', $providerAccountId)
                    ->whereNotIn('status', $omittedServiceStatuses);
            })
            ->whereNotIn('status', $omittedVariantStatuses)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($proposedVariantIds as $vid) {
            abort_unless(in_array($vid, $allowedVariantIds, true), 422);
        }

        foreach ($proposedServiceIds as $sid) {
            abort_unless(in_array($sid, $allowedServiceIds, true), 422);
        }

        $newPendingCount = 0;

        DB::transaction(function () use (
            $providerAccountId,
            $operatorAccountId,
            $proposedVariantIds,
            $proposedServiceIds,
            $allowedServiceIds,
            $allowedVariantIds,
            $omittedServiceStatuses,
            $omittedVariantStatuses,
            &$newPendingCount,
        ): void {
            $newPendingCount += $this->syncServiceOffersInTransaction(
                $providerAccountId,
                $operatorAccountId,
                $proposedServiceIds,
                $allowedServiceIds,
                $omittedServiceStatuses,
            );

            $newPendingCount += $this->syncVariantOffersInTransaction(
                $providerAccountId,
                $operatorAccountId,
                $proposedVariantIds,
                $allowedVariantIds,
                $omittedServiceStatuses,
                $omittedVariantStatuses,
            );
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

    /**
     * @param  list<int>  $proposedVariantIds
     * @return array{new_pending_count: int}
     */
    public function syncVariantProposals(
        int $providerAccountId,
        int $operatorAccountId,
        array $proposedVariantIds,
        User $actingUser,
    ): array {
        return $this->syncProposals($providerAccountId, $operatorAccountId, $proposedVariantIds, [], $actingUser);
    }

    /**
     * @param  list<int>  $proposedServiceIds
     * @param  list<int>  $allowedServiceIds
     * @param  list<string>  $omittedServiceStatuses
     */
    private function syncServiceOffersInTransaction(
        int $providerAccountId,
        int $operatorAccountId,
        array $proposedServiceIds,
        array $allowedServiceIds,
        array $omittedServiceStatuses,
    ): int {
        $acceptedServiceIds = ServiceOffer::query()
            ->where('provider_id', $providerAccountId)
            ->where('operator_id', $operatorAccountId)
            ->whereNull('service_variant_id')
            ->whereNotNull('service_id')
            ->where('status', ServiceOffer::STATUS_ACCEPTED)
            ->pluck('service_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $wantServiceIds = collect($proposedServiceIds)
            ->merge($acceptedServiceIds)
            ->unique()
            ->values()
            ->all();

        ServiceOffer::query()
            ->where('provider_id', $providerAccountId)
            ->where('operator_id', $operatorAccountId)
            ->whereNull('service_variant_id')
            ->whereNotNull('service_id')
            ->whereIn('status', [
                ServiceOffer::STATUS_PENDING,
                ServiceOffer::STATUS_REJECTED,
            ])
            ->whereHas('service', fn ($sq) => $sq->whereIn('status', $omittedServiceStatuses))
            ->delete();

        $servicesById = Service::query()
            ->whereIn('id', $allowedServiceIds)
            ->get()
            ->keyBy(fn (Service $s) => (int) $s->id);

        $offers = ServiceOffer::query()
            ->where('provider_id', $providerAccountId)
            ->where('operator_id', $operatorAccountId)
            ->whereNull('service_variant_id')
            ->whereNotNull('service_id')
            ->whereIn('service_id', $allowedServiceIds)
            ->get()
            ->keyBy(fn (ServiceOffer $o) => (int) $o->service_id);

        $newPendingCount = 0;

        foreach ($allowedServiceIds as $serviceId) {
            $want = in_array($serviceId, $wantServiceIds, true);
            /** @var ServiceOffer|null $offer */
            $offer = $offers->get($serviceId);

            if ($want) {
                if ($offer === null) {
                    ServiceOffer::query()->create([
                        'provider_id' => $providerAccountId,
                        'operator_id' => $operatorAccountId,
                        'service_id' => $serviceId,
                        'service_variant_id' => null,
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
                /** @var Service|null $service */
                $service = $servicesById->get($serviceId);
                if ($service !== null && ! $service->catalogSelectableForOperatorOffers()) {
                    continue;
                }
                $offer->delete();
            }
        }

        return $newPendingCount;
    }

    /**
     * @param  list<int>  $proposedVariantIds
     * @param  list<int>  $allowedVariantIds
     * @param  list<string>  $omittedServiceStatuses
     * @param  list<string>  $omittedVariantStatuses
     */
    private function syncVariantOffersInTransaction(
        int $providerAccountId,
        int $operatorAccountId,
        array $proposedVariantIds,
        array $allowedVariantIds,
        array $omittedServiceStatuses,
        array $omittedVariantStatuses,
    ): int {
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

        ServiceOffer::query()
            ->where('provider_id', $providerAccountId)
            ->where('operator_id', $operatorAccountId)
            ->whereNotNull('service_variant_id')
            ->whereIn('status', [
                ServiceOffer::STATUS_PENDING,
                ServiceOffer::STATUS_REJECTED,
            ])
            ->where(function ($q) use ($omittedVariantStatuses, $omittedServiceStatuses): void {
                $q->whereHas('serviceVariant', fn ($vq) => $vq->whereIn('status', $omittedVariantStatuses))
                    ->orWhereHas('serviceVariant.service', fn ($sq) => $sq->whereIn('status', $omittedServiceStatuses));
            })
            ->delete();

        $variantsById = ServiceVariant::query()
            ->whereIn('id', $allowedVariantIds)
            ->with('service')
            ->get()
            ->keyBy(fn (ServiceVariant $v) => (int) $v->id);

        $offers = ServiceOffer::query()
            ->where('provider_id', $providerAccountId)
            ->where('operator_id', $operatorAccountId)
            ->whereNotNull('service_variant_id')
            ->whereIn('service_variant_id', $allowedVariantIds)
            ->get()
            ->keyBy(fn (ServiceOffer $o) => (int) $o->service_variant_id);

        $newPendingCount = 0;

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
                /** @var ServiceVariant|null $variant */
                $variant = $variantsById->get($variantId);
                if ($variant !== null && ! $variant->catalogSelectableForOperatorOffers()) {
                    continue;
                }
                $offer->delete();
            }
        }

        return $newPendingCount;
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
