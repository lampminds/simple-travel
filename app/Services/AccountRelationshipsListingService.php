<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\AccountRelationship;
use App\Models\Service;
use App\Models\ServiceOffer;
use App\Models\ServiceVariant;
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
     * Accepted services and combos versus the provider catalog, for one operator.
     *
     * A catalog item counts as accepted when at least one of its variants has an
     * accepted offer for this operator. Total is every service or combo the
     * provider still has available to offer (discontinued and terminated entries
     * are left out, same as the offer picker).
     *
     * @param  Collection<int, array<string, mixed>>  $rows
     * @return Collection<int, array<string, mixed>>
     */
    public function attachProviderCatalogCoverage(Collection $rows, int $operatorAccountId): Collection
    {
        $providerIds = $rows
            ->filter(fn (array $row): bool => ($row['counterpart_kind'] ?? '') === 'provider')
            ->map(fn (array $row): int => (int) $row['counterpart']->id)
            ->unique()
            ->values()
            ->all();

        $coverage = $this->providerCatalogCoverage($operatorAccountId, $providerIds);

        return $rows->map(function (array $row) use ($coverage): array {
            if (($row['counterpart_kind'] ?? '') !== 'provider') {
                return $row;
            }

            $providerId = (int) $row['counterpart']->id;
            $counts = $coverage[$providerId] ?? ['accepted' => 0, 'total' => 0];
            $row['catalog_accepted'] = $counts['accepted'];
            $row['catalog_total'] = $counts['total'];

            return $row;
        });
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

    /**
     * @param  list<int>  $providerAccountIds
     * @return array<int, array{accepted: int, total: int}>
     */
    private function providerCatalogCoverage(int $operatorAccountId, array $providerAccountIds): array
    {
        $providerAccountIds = array_values(array_unique(array_filter(
            array_map('intval', $providerAccountIds),
            fn (int $id): bool => $id > 0,
        )));

        $coverage = [];
        foreach ($providerAccountIds as $providerId) {
            $coverage[$providerId] = ['accepted' => 0, 'total' => 0];
        }

        if ($providerAccountIds === []) {
            return $coverage;
        }

        $omittedServiceStatuses = Service::catalogStatusesOmittedFromOperatorOffers();
        $omittedVariantStatuses = ServiceVariant::catalogStatusesOmittedFromOperatorOffers();

        $totals = Service::query()
            ->whereIn('account_id', $providerAccountIds)
            ->whereNotIn('status', $omittedServiceStatuses)
            ->whereHas(
                'serviceVariants',
                fn ($query) => $query->whereNotIn('status', $omittedVariantStatuses),
            )
            ->selectRaw('account_id, COUNT(*) as catalog_total')
            ->groupBy('account_id')
            ->pluck('catalog_total', 'account_id');

        foreach ($totals as $providerId => $total) {
            $providerId = (int) $providerId;
            if (! isset($coverage[$providerId])) {
                continue;
            }

            $coverage[$providerId]['total'] = (int) $total;
        }

        $accepted = ServiceOffer::query()
            ->join('service_variants', 'service_offers.service_variant_id', '=', 'service_variants.id')
            ->join('services', 'service_variants.service_id', '=', 'services.id')
            ->where('service_offers.operator_id', $operatorAccountId)
            ->whereIn('service_offers.provider_id', $providerAccountIds)
            ->where('service_offers.status', ServiceOffer::STATUS_ACCEPTED)
            ->whereNotIn('services.status', $omittedServiceStatuses)
            ->whereNotIn('service_variants.status', $omittedVariantStatuses)
            ->selectRaw('service_offers.provider_id, COUNT(DISTINCT services.id) as catalog_accepted')
            ->groupBy('service_offers.provider_id')
            ->pluck('catalog_accepted', 'provider_id');

        foreach ($accepted as $providerId => $count) {
            $providerId = (int) $providerId;
            if (! isset($coverage[$providerId])) {
                continue;
            }

            $coverage[$providerId]['accepted'] = (int) $count;
        }

        return $coverage;
    }
}
