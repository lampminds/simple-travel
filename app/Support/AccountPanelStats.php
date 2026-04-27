<?php

namespace App\Support;

use App\Models\Account;
use App\Models\Service;
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
            'catalog_service_count' => Service::query()->where('account_id', $accountId)->count(),
            'catalog_variant_count' => ServiceVariant::query()
                ->whereHas('service', function ($q) use ($accountId) {
                    $q->where('account_id', $accountId);
                })
                ->count(),
        ];
    }
}
