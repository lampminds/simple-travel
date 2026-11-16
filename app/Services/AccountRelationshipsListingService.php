<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\AccountRelationship;
use App\Models\ProviderServiceOffer;
use App\Models\ProviderSupplyRelationship;
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
     * Commercial links and provider-to-provider links in one list.
     *
     * Each row is one relationship. The same company can appear more than once
     * when the link type is different (operator, agency, or provider supply).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function unifiedForAccount(Account $account): Collection
    {
        $accountId = (int) $account->id;
        $typeCodes = $account->accountTypes()
            ->where('cat_account_types.active', true)
            ->pluck('cat_account_types.code');
        $hasProvider = $typeCodes->contains('provider');
        $hasAgency = $typeCodes->contains('agency');

        $rows = $this->forAccount($accountId)->map(function (array $row) use ($accountId, $hasProvider, $hasAgency): array {
            $relationship = $row['relationship'];
            $isSellingSide = (int) $relationship->provider_account_id === $accountId;

            if ($isSellingSide && $hasAgency && ! $hasProvider) {
                $row['link_kind'] = 'sells_as_agency';
                $row['viewer_role'] = 'agency';
            } elseif ($isSellingSide) {
                $row['link_kind'] = 'sells_to_operator';
                $row['viewer_role'] = 'provider';
                $row['also_agency'] = $hasAgency;
            } elseif (($row['counterpart_kind'] ?? '') === 'agency') {
                $row['link_kind'] = 'sells_to_agency';
                $row['viewer_role'] = 'operator';
            } else {
                $row['link_kind'] = 'buys_from_provider';
                $row['viewer_role'] = 'operator';
            }

            $row['pending_offers'] = 0;

            return $row;
        });

        if ($hasProvider) {
            $rows = $rows->concat($this->supplyRows($accountId));
        }

        return $rows
            ->sortBy([
                fn (array $row): int => match ((string) $row['relationship']->status) {
                    'approved' => 0,
                    'suspended' => 1,
                    default => 2,
                },
                fn (array $row): string => mb_strtolower((string) $row['counterpart_label']),
            ])
            ->values();
    }

    /**
     * Pending supply invitations this account sent and has not resolved yet.
     *
     * @return Collection<int, UserInvitation>
     */
    public function pendingOutgoingSupplyInvitations(int $accountId): Collection
    {
        UserInvitation::syncExpiredForAccount($accountId);

        return UserInvitation::query()
            ->where('account_id', $accountId)
            ->where('purpose', UserInvitation::PURPOSE_PROVIDER_SUPPLY)
            ->where('status', UserInvitation::STATUS_PENDING)
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->orderByDesc('id')
            ->get();
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
            ->get()
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

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function supplyRows(int $accountId): Collection
    {
        $relationships = ProviderSupplyRelationship::query()
            ->where(function ($query) use ($accountId): void {
                $query->where('buyer_account_id', $accountId)
                    ->orWhere('supplier_account_id', $accountId);
            })
            ->with(['buyerAccount.accountTypes', 'supplierAccount.accountTypes'])
            ->get();

        $supplierIds = $relationships
            ->filter(fn (ProviderSupplyRelationship $relationship): bool => (int) $relationship->buyer_account_id === $accountId)
            ->map(fn (ProviderSupplyRelationship $relationship): int => (int) $relationship->supplier_account_id)
            ->unique()
            ->values()
            ->all();

        $pendingOffers = $supplierIds === []
            ? collect()
            : ProviderServiceOffer::query()
                ->where('buyer_id', $accountId)
                ->where('status', ProviderServiceOffer::STATUS_PENDING)
                ->whereIn('supplier_id', $supplierIds)
                ->selectRaw('supplier_id, COUNT(*) as aggregate')
                ->groupBy('supplier_id')
                ->pluck('aggregate', 'supplier_id');

        return $relationships
            ->map(function (ProviderSupplyRelationship $relationship) use ($accountId, $pendingOffers): ?array {
                $isBuyer = (int) $relationship->buyer_account_id === $accountId;
                $counterpart = $isBuyer ? $relationship->supplierAccount : $relationship->buyerAccount;
                if (! $counterpart instanceof Account) {
                    return null;
                }

                $supplierId = (int) $relationship->supplier_account_id;

                return [
                    'relationship' => $relationship,
                    'viewer_role' => $isBuyer ? 'supply_buyer' : 'supply_supplier',
                    'counterpart' => $counterpart,
                    'counterpart_label' => $this->accountDisplayName($counterpart),
                    'counterpart_kind' => 'supply',
                    'link_kind' => $isBuyer ? 'supply_supplier' : 'supply_buyer',
                    'also_agency' => false,
                    'pending_offers' => $isBuyer ? (int) ($pendingOffers[$supplierId] ?? 0) : 0,
                ];
            })
            ->filter()
            ->values();
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
