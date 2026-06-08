<?php

namespace App\Support;

use App\Models\Account;
use App\Models\OperatorServiceCatalog;
use App\Models\Service;
use App\Models\ServiceOffer;
use App\Models\ServiceVariant;
use App\Models\UserInvitation;

/**
 * Small numeric summaries for operator/provider home dashboards (invitations + catalog).
 */
final class AccountPanelStats
{
    /**
     * @return array{
     *     invitations_pending_employee: int,
     *     invitations_pending_company: int,
     *     catalog_service_count: int,
     *     catalog_variant_count: int,
     * }
     */
    public static function forAccount(Account $account): array
    {
        return array_merge(
            static::invitationCounts($account),
            [
                'catalog_service_count' => Service::query()->where('account_id', $account->id)->count(),
                'catalog_variant_count' => ServiceVariant::query()
                    ->whereHas('service', function ($q) use ($account) {
                        $q->where('account_id', $account->id);
                    })
                    ->count(),
            ],
        );
    }

    /**
     * Operator dashboard: invitations + commercial packages only.
     *
     * @return array{
     *     invitations_pending_employee: int,
     *     invitations_pending_company: int,
     *     operator_package_count: int,
     *     service_offers_pending_count: int,
     * }
     */
    public static function forOperator(Account $account): array
    {
        return array_merge(
            static::invitationCounts($account),
            [
                'operator_package_count' => OperatorServiceCatalog::query()
                    ->where('operator_id', $account->id)
                    ->count(),
                'service_offers_pending_count' => ServiceOffer::query()
                    ->where('operator_id', $account->id)
                    ->where('status', ServiceOffer::STATUS_PENDING)
                    ->whereHas('serviceVariant')
                    ->count(),
            ],
        );
    }

    /**
     * @return array{invitations_pending_employee: int, invitations_pending_company: int}
     */
    private static function invitationCounts(Account $account): array
    {
        UserInvitation::syncExpiredForAccount($account->id);

        $accountId = $account->id;

        $invitationPending = static function (string $type) use ($accountId): int {
            return UserInvitation::query()
                ->where('account_id', $accountId)
                ->where('type', $type)
                ->where('status', UserInvitation::STATUS_PENDING)
                ->whereNotNull('expires_at')
                ->where('expires_at', '>', now())
                ->count();
        };

        return [
            'invitations_pending_employee' => $invitationPending(UserInvitation::TYPE_INTERNAL),
            'invitations_pending_company' => $invitationPending(UserInvitation::TYPE_EXTERNAL),
        ];
    }
}
