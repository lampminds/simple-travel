<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OperatorPackageAvailabilityOverride;
use App\Models\OperatorServiceCatalog;
use App\Models\PackageAllocation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Ensures hard agency allocations do not exceed package inventory (including date overrides).
 */
final class PackageAllocationInventoryCapacityService
{
    private const OPEN_START = '0001-01-01';

    private const OPEN_END = '9999-12-31';

    /**
     * @return array{date: string, assigned: int, limit: int, reason?: string}|null
     */
    public function findInventoryViolation(
        OperatorServiceCatalog $catalog,
        int $proposedCapacity,
        ?string $proposedStartDate,
        ?string $proposedEndDate,
        bool $proposedActive,
        string $proposedType,
        ?int $excludeAllocationId = null,
    ): ?array {
        if ($proposedType !== PackageAllocation::TYPE_HARD || ! $proposedActive) {
            return null;
        }

        if ($catalog->inventory_type === 'unlimited') {
            return null;
        }

        if ($catalog->inventory_total === null) {
            return [
                'date' => '',
                'assigned' => $proposedCapacity,
                'limit' => 0,
                'reason' => 'missing_inventory_total',
            ];
        }

        $allocations = $this->activeHardAllocations((int) $catalog->id, $excludeAllocationId);

        $proposed = new PackageAllocation([
            'allocation_type' => PackageAllocation::TYPE_HARD,
            'capacity' => $proposedCapacity,
            'start_date' => $proposedStartDate,
            'end_date' => $proposedEndDate,
            'active' => true,
        ]);

        $allocations->push($proposed);

        $scanStart = $this->openDate($proposedStartDate);
        $scanEnd = $this->openDate($proposedEndDate, false);

        $breakpointDates = $this->breakpointDates($allocations, $scanStart, $scanEnd);
        $overrides = $this->wholeDayOverridesForRange((int) $catalog->id, $scanStart, $scanEnd);

        foreach ($overrides->keys() as $overrideDate) {
            if ($overrideDate >= $scanStart && $overrideDate <= $scanEnd) {
                $breakpointDates[] = $overrideDate;
            }
        }

        $breakpointDates = array_values(array_unique($breakpointDates));
        sort($breakpointDates);

        foreach ($breakpointDates as $day) {
            if ($day < $scanStart || $day > $scanEnd) {
                continue;
            }

            $assigned = $this->assignedHardCapacityOnDay($allocations, $day);
            $limit = $this->inventoryLimitOnDay($catalog, $day, $overrides);

            if ($limit === null) {
                continue;
            }

            if ($assigned > $limit) {
                return [
                    'date' => $day,
                    'assigned' => $assigned,
                    'limit' => $limit,
                    'reason' => 'exceeds',
                ];
            }
        }

        return null;
    }

    /**
     * @return Collection<int, PackageAllocation>
     */
    private function activeHardAllocations(int $catalogId, ?int $excludeAllocationId): Collection
    {
        $query = PackageAllocation::query()
            ->where('operator_service_catalog_id', $catalogId)
            ->where('active', true)
            ->where('allocation_type', PackageAllocation::TYPE_HARD);

        if ($excludeAllocationId !== null) {
            $query->where('id', '!=', $excludeAllocationId);
        }

        return $query->get();
    }

    /**
     * @param  Collection<int, PackageAllocation>  $allocations
     * @return list<string>
     */
    private function breakpointDates(Collection $allocations, string $rangeStart, string $rangeEnd): array
    {
        $dates = [$rangeStart];

        foreach ($allocations as $allocation) {
            $start = $allocation->start_date?->format('Y-m-d') ?? self::OPEN_START;
            $end = $allocation->end_date?->format('Y-m-d') ?? self::OPEN_END;

            if ($start >= $rangeStart && $start <= $rangeEnd) {
                $dates[] = $start;
            }

            if ($end < self::OPEN_END) {
                $dayAfterEnd = Carbon::parse($end)->addDay()->toDateString();
                if ($dayAfterEnd >= $rangeStart && $dayAfterEnd <= $rangeEnd) {
                    $dates[] = $dayAfterEnd;
                }
            }
        }

        $dates = array_values(array_unique($dates));
        sort($dates);

        return $dates;
    }

    /**
     * @return Collection<string, OperatorPackageAvailabilityOverride>
     */
    private function wholeDayOverridesForRange(int $catalogId, string $rangeStart, string $rangeEnd): Collection
    {
        return OperatorPackageAvailabilityOverride::query()
            ->where('operator_service_catalog_id', $catalogId)
            ->whereNull('start_time')
            ->whereDate('date', '>=', $rangeStart)
            ->whereDate('date', '<=', $rangeEnd)
            ->get()
            ->keyBy(fn (OperatorPackageAvailabilityOverride $override): string => $override->date->format('Y-m-d'));
    }

    /**
     * @param  Collection<int, PackageAllocation>  $allocations
     */
    private function assignedHardCapacityOnDay(Collection $allocations, string $day): int
    {
        $sum = 0;

        foreach ($allocations as $allocation) {
            if ($allocation->allocation_type !== PackageAllocation::TYPE_HARD || ! $allocation->active) {
                continue;
            }

            $start = $allocation->start_date?->format('Y-m-d') ?? self::OPEN_START;
            $end = $allocation->end_date?->format('Y-m-d') ?? self::OPEN_END;

            if ($day >= $start && $day <= $end) {
                $sum += (int) $allocation->capacity;
            }
        }

        return $sum;
    }

    /**
     * @param  Collection<string, OperatorPackageAvailabilityOverride>  $overridesByDate
     */
    private function inventoryLimitOnDay(
        OperatorServiceCatalog $catalog,
        string $day,
        Collection $overridesByDate,
    ): ?int {
        if ($catalog->inventory_type === 'unlimited') {
            return null;
        }

        $override = $overridesByDate->get($day);
        if ($override instanceof OperatorPackageAvailabilityOverride) {
            if ($override->closed) {
                return 0;
            }

            if ($override->capacity !== null) {
                return (int) $override->capacity;
            }
        }

        return $catalog->inventory_total !== null ? (int) $catalog->inventory_total : null;
    }

    private function openDate(?string $date, bool $isStart = true): string
    {
        if ($date !== null && $date !== '') {
            return $date;
        }

        return $isStart ? self::OPEN_START : self::OPEN_END;
    }
}
