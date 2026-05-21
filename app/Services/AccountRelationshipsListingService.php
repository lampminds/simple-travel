<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\AccountRelationship;
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
     *     counterpart_label: string
     * }>
     */
    public function forAccount(int $accountId, ?string $perspective = null): Collection
    {
        $perspective = $this->normalizePerspective($perspective);

        $query = AccountRelationship::query()
            ->with(['operatorAccount', 'providerAccount'])
            ->orderByRaw("CASE status WHEN 'approved' THEN 0 WHEN 'suspended' THEN 1 ELSE 2 END")
            ->orderByDesc('approved_at')
            ->orderBy('id');

        if ($perspective === 'provider') {
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
            ->map(function (AccountRelationship $relationship) use ($accountId): ?array {
                $isProviderSide = (int) $relationship->provider_account_id === $accountId;
                $counterpart = $isProviderSide
                    ? $relationship->operatorAccount
                    : $relationship->providerAccount;

                if (! $counterpart instanceof Account) {
                    return null;
                }

                return [
                    'relationship' => $relationship,
                    'viewer_role' => $isProviderSide ? 'provider' : 'operator',
                    'counterpart' => $counterpart,
                    'counterpart_label' => $this->accountDisplayName($counterpart),
                ];
            })
            ->filter()
            ->values();
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

        return in_array($perspective, ['provider', 'operator'], true) ? $perspective : null;
    }
}
