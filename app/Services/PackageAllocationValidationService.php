<?php

namespace App\Services;

use App\Models\OperatorServiceCatalog;
use App\Models\PackageAllocation;
use App\Models\PackageOffer;

final class PackageAllocationValidationService
{
    /**
     * Accepted-offer catalog targets eligible for allocation to an agency.
     *
     * @return array{catalogs: array<int, string>}
     */
    public function eligibleTargetOptions(int $operatorId, int $agencyId): array
    {
        $offers = PackageOffer::query()
            ->where('operator_id', $operatorId)
            ->where('agency_id', $agencyId)
            ->where('status', PackageOffer::STATUS_ACCEPTED)
            ->with([
                'catalog.translations.language.locale',
            ])
            ->get();

        $catalogs = [];

        foreach ($offers as $offer) {
            $catalog = $offer->catalog;
            if ($catalog === null) {
                continue;
            }
            $catalogs[(int) $catalog->id] = $this->catalogLabel($catalog);
        }

        asort($catalogs);

        return [
            'catalogs' => $catalogs,
        ];
    }

    public function targetHasAcceptedOffer(int $operatorId, int $agencyId, ?int $catalogId): bool
    {
        if ($catalogId === null || $catalogId <= 0) {
            return false;
        }

        return PackageOffer::query()
            ->where('operator_id', $operatorId)
            ->where('agency_id', $agencyId)
            ->where('operator_service_catalog_id', $catalogId)
            ->where('status', PackageOffer::STATUS_ACCEPTED)
            ->exists();
    }

    /**
     * @return array{operator_service_catalog_id: int}|null
     */
    public function parseTargetKey(string $targetKey): ?array
    {
        $targetKey = trim($targetKey);
        if (preg_match('/^catalog:(\d+)$/', $targetKey, $matches) === 1) {
            return [
                'operator_service_catalog_id' => (int) $matches[1],
            ];
        }

        return null;
    }

    public function targetKeyFromAllocation(PackageAllocation $allocation): string
    {
        return 'catalog:'.(int) $allocation->operator_service_catalog_id;
    }

    public function findOverlappingAllocation(
        int $operatorId,
        int $agencyId,
        int $catalogId,
        ?string $startDate,
        ?string $endDate,
        ?int $excludeAllocationId = null,
    ): ?PackageAllocation {
        $query = PackageAllocation::query()
            ->where('operator_id', $operatorId)
            ->where('agency_id', $agencyId)
            ->where('operator_service_catalog_id', $catalogId);

        if ($excludeAllocationId !== null) {
            $query->where('id', '!=', $excludeAllocationId);
        }

        /** @var \Illuminate\Support\Collection<int, PackageAllocation> $candidates */
        $candidates = $query->get();

        foreach ($candidates as $existing) {
            if ($this->dateRangesOverlap(
                $startDate,
                $endDate,
                $existing->start_date?->format('Y-m-d'),
                $existing->end_date?->format('Y-m-d'),
            )) {
                return $existing;
            }
        }

        return null;
    }

    public function allocationTargetLabel(PackageAllocation $allocation): string
    {
        $catalog = $allocation->catalog;
        if ($catalog instanceof OperatorServiceCatalog) {
            return $this->catalogLabel($catalog);
        }

        return __('account.package_allocations.target_catalog_fallback', ['id' => $allocation->operator_service_catalog_id]);
    }

    private function catalogLabel(OperatorServiceCatalog $catalog): string
    {
        return $catalog->displayLabel();
    }

    private function dateRangesOverlap(?string $startA, ?string $endA, ?string $startB, ?string $endB): bool
    {
        $minDate = '0001-01-01';
        $maxDate = '9999-12-31';

        $startA = $startA ?? $minDate;
        $endA = $endA ?? $maxDate;
        $startB = $startB ?? $minDate;
        $endB = $endB ?? $maxDate;

        if ($startA > $endA || $startB > $endB) {
            return false;
        }

        return $startA <= $endB && $startB <= $endA;
    }
}
