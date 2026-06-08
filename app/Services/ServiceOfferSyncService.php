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
 * Syncs variant-level offers from a provider to an operator (pending until accepted).
 */
final class ServiceOfferSyncService
{
    public function __construct(
        private readonly AccountNotificationService $accountNotifications,
    ) {}

    /**
     * @param  list<int>  $proposedVariantIds
     * @return array{new_pending_count: int}
     */
    public function syncProposals(
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

        $omittedServiceStatuses = Service::catalogStatusesOmittedFromOperatorOffers();
        $omittedVariantStatuses = ServiceVariant::catalogStatusesOmittedFromOperatorOffers();

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

        $newPendingCount = 0;

        DB::transaction(function () use (
            $providerAccountId,
            $operatorAccountId,
            $proposedVariantIds,
            $allowedVariantIds,
            $omittedServiceStatuses,
            $omittedVariantStatuses,
            &$newPendingCount,
        ): void {
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

    public function notifyProviderOfAcceptedOffer(ServiceOffer $offer, User $actingUser): void
    {
        $offer->loadMissing([
            'serviceVariant.service.translations',
            'serviceVariant.translations.language.locale',
            'operatorAccount',
        ]);

        $providerAccountId = (int) $offer->provider_id;
        $operatorAccountId = (int) $offer->operator_id;
        $operator = $offer->operatorAccount ?? Account::query()->find($operatorAccountId);
        $operatorLabel = $operator?->commercial_name ?? $operator?->name ?? $operator?->nick ?? (string) $operatorAccountId;

        $variantLabel = $this->variantLabelForOffer($offer);

        $url = $operator instanceof Account
            ? route('account.service-offers.operators.edit', ['operator' => $operator], true)
            : route('account.service-offers.index', ['as' => 'provider'], true);

        $this->accountNotifications->createForAccount(
            accountId: $providerAccountId,
            type: 'service_offer_accepted',
            title: (string) __('account.service_offers.notification_accepted_title', ['operator' => $operatorLabel]),
            message: (string) __('account.service_offers.notification_accepted_message', [
                'operator' => $operatorLabel,
                'variant' => $variantLabel,
                'url' => $url,
            ]),
            recipientUserId: null,
            data: [
                'operator_account_id' => $operatorAccountId,
                'service_offer_id' => (int) $offer->id,
                'variant_label' => $variantLabel,
                'created_by_user_id' => $actingUser->id,
                'created_by_user_name' => $actingUser->name,
            ],
        );
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
        return $this->syncProposals($providerAccountId, $operatorAccountId, $proposedVariantIds, $actingUser);
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
            ->where('status', ServiceOffer::STATUS_ACCEPTED)
            ->pluck('service_variant_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $pendingVariantIds = ServiceOffer::query()
            ->where('provider_id', $providerAccountId)
            ->where('operator_id', $operatorAccountId)
            ->where('status', ServiceOffer::STATUS_PENDING)
            ->pluck('service_variant_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $wantVariantIds = collect($proposedVariantIds)
            ->merge($acceptedVariantIds)
            ->merge($pendingVariantIds)
            ->unique()
            ->values()
            ->all();

        ServiceOffer::query()
            ->where('provider_id', $providerAccountId)
            ->where('operator_id', $operatorAccountId)
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
            } elseif ($offer !== null && $offer->status === ServiceOffer::STATUS_REJECTED) {
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

    public function revokePendingProposal(
        int $providerAccountId,
        int $operatorAccountId,
        int $serviceOfferId,
        User $actingUser,
    ): void {
        abort_unless(
            AccountRelationship::query()
                ->where('provider_account_id', $providerAccountId)
                ->where('operator_account_id', $operatorAccountId)
                ->where('status', AccountRelationship::STATUS_APPROVED)
                ->exists(),
            403
        );

        $offer = ServiceOffer::query()
            ->where('id', $serviceOfferId)
            ->where('provider_id', $providerAccountId)
            ->where('operator_id', $operatorAccountId)
            ->where('status', ServiceOffer::STATUS_PENDING)
            ->with([
                'serviceVariant.service.translations',
                'serviceVariant.translations.language.locale',
            ])
            ->first();

        abort_unless($offer !== null, 404);

        $variantLabel = $this->variantLabelForOffer($offer);

        DB::transaction(function () use ($offer): void {
            $offer->update(['withdrawn_at' => now()]);
            $offer->delete();
        });

        $this->notifyOperatorOfRevokedProposal(
            $providerAccountId,
            $operatorAccountId,
            $variantLabel,
            $actingUser,
        );
    }

    private function variantLabelForOffer(ServiceOffer $offer): string
    {
        $variant = $offer->serviceVariant;
        $service = $variant?->service;
        if ($variant === null || $service === null) {
            return '—';
        }

        $serviceName = trim((string) ($service->name ?? ''));
        if ($serviceName === '') {
            $serviceName = 'Service #'.$service->id;
        }

        $detail = trim((string) ($variant->name ?? ''));
        if ($detail === '') {
            $detail = trim((string) ($variant->sku ?? ''));
        }
        if ($detail === '') {
            $detail = 'Variant #'.$variant->id;
        }

        if (strcasecmp($detail, $serviceName) === 0) {
            return $serviceName;
        }

        return $serviceName.' — '.$detail;
    }

    private function notifyOperatorOfRevokedProposal(
        int $providerAccountId,
        int $operatorAccountId,
        string $variantLabel,
        User $actingUser,
    ): void {
        $provider = Account::query()->find($providerAccountId);
        $providerLabel = $provider?->commercial_name ?? $provider?->name ?? $provider?->nick ?? (string) $providerAccountId;

        $url = route('account.service-offers.index', ['as' => 'operator'], true);

        $this->accountNotifications->createForAccount(
            accountId: $operatorAccountId,
            type: 'service_offer_revoked',
            title: (string) __('account.service_offers.notification_revoked_title', ['provider' => $providerLabel]),
            message: (string) __('account.service_offers.notification_revoked_message', [
                'provider' => $providerLabel,
                'variant' => $variantLabel,
                'url' => $url,
            ]),
            recipientUserId: null,
            data: [
                'provider_account_id' => $providerAccountId,
                'variant_label' => $variantLabel,
                'created_by_user_id' => $actingUser->id,
                'created_by_user_name' => $actingUser->name,
            ],
        );
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
