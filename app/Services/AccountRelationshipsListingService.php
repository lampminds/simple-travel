<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\AccountRelationship;
use App\Models\UserInvitation;
use Illuminate\Support\Collection;

/**
 * Lists commercial relationships for an account as provider and/or operator.
 */
final class AccountRelationshipsListingService
{
    /**
     * @return Collection<int, array{
     *     relationship: AccountRelationship,
     *     viewer_role: string,
     *     counterpart: Account,
     *     counterpart_label: string,
     *     counterpart_kind: string
     * }>
     */
    public function forAccount(int $accountId, ?string $perspective = null, ?string $counterpartKind = null): Collection
    {
        $perspective = $this->normalizePerspective($perspective);
        $counterpartKind = $this->normalizeCounterpartKind($counterpartKind);

        $query = AccountRelationship::query()
            ->with(['operatorAccount.accountTypes', 'providerAccount.accountTypes'])
            ->orderByRaw("CASE status WHEN 'approved' THEN 0 WHEN 'suspended' THEN 1 ELSE 2 END")
            ->orderByDesc('approved_at')
            ->orderBy('id');

        if ($perspective === 'provider' || $perspective === 'agency') {
            $query->where('provider_account_id', $accountId);
        } elseif ($perspective === 'operator') {
            $query->where('operator_account_id', $accountId);
        } else {
            $query->where(function ($q) use ($accountId): void {
                $q->where('provider_account_id', $accountId)
                    ->orWhere('operator_account_id', $accountId);
            });
        }

        return $query->get()
            ->map(function (AccountRelationship $relationship) use ($accountId, $perspective, $counterpartKind): ?array {
                $isProviderSide = (int) $relationship->provider_account_id === $accountId;
                $counterpart = $isProviderSide
                    ? $relationship->operatorAccount
                    : $relationship->providerAccount;

                if (! $counterpart instanceof Account) {
                    return null;
                }

                $viewerRole = $isProviderSide
                    ? ($perspective === 'agency' ? 'agency' : 'provider')
                    : 'operator';

                $row = [
                    'relationship' => $relationship,
                    'viewer_role' => $viewerRole,
                    'counterpart' => $counterpart,
                    'counterpart_label' => $this->accountDisplayName($counterpart),
                    'counterpart_kind' => $this->resolveCounterpartKind($counterpart),
                ];

                if ($counterpartKind !== null && $row['counterpart_kind'] !== $counterpartKind) {
                    return null;
                }

                return $row;
            })
            ->filter()
            ->values();
    }

    /**
     * Pending external invitations addressed to this company (existing-user flow).
     *
     * @return Collection<int, UserInvitation>
     */
    public function pendingIncomingExternalInvitations(int $providerAccountId): Collection
    {
        UserInvitation::syncExpiredForAccount();

        return UserInvitation::query()
            ->where('type', UserInvitation::TYPE_EXTERNAL)
            ->where('status', UserInvitation::STATUS_PENDING)
            ->where('invited_account_id', $providerAccountId)
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->with(['accountInviting', 'account', 'invitedBy'])
            ->orderByDesc('id')
            ->get();
    }

    public function accountDisplayName(Account $account): string
    {
        $label = $account->commercial_name ?? $account->name ?? $account->nick;

        if (is_string($label) && trim($label) !== '') {
            return trim($label);
        }

        return '#'.$account->id;
    }

    private function normalizePerspective(?string $perspective): ?string
    {
        if ($perspective === null) {
            return null;
        }

        $perspective = strtolower(trim($perspective));

        return in_array($perspective, ['provider', 'operator', 'agency'], true) ? $perspective : null;
    }

    private function normalizeCounterpartKind(?string $counterpartKind): ?string
    {
        if ($counterpartKind === null) {
            return null;
        }

        $counterpartKind = strtolower(trim($counterpartKind));

        return in_array($counterpartKind, ['provider', 'agency'], true) ? $counterpartKind : null;
    }

    private function resolveCounterpartKind(Account $counterpart): string
    {
        $codes = $counterpart->accountTypes
            ->where('active', true)
            ->pluck('code');

        if ($codes->contains('agency')) {
            return 'agency';
        }

        if ($codes->contains('provider')) {
            return 'provider';
        }

        return 'other';
    }
}
