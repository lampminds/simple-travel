<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Allocation;
use App\Models\ServiceVariant;
use App\Models\ServiceVariantAvailabilityOverride;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Ensures hard operator allocations do not exceed variant inventory (including date overrides).
 */
final class AllocationInventoryCapacityService
{
    private const OPEN_START = '0001-01-01';

    private const OPEN_END = '9999-12-31';

    /**
     * @return array{date: string, assigned: int, limit: int}|null
     */
    public function findInventoryViolation(
        ServiceVariant $variant,
        int $proposedCapacity,
        ?string $proposedStartDate,
        ?string $proposedEndDate,
        bool $proposedActive,
        string $proposedType,
        ?int $excludeAllocationId = null,
    ): ?array {
        if ($proposedType !== Allocation::TYPE_HARD || ! $proposedActive) {
            return null;
        }

        if ($variant->inventory_type === 'unlimited') {
            return null;
        }

        if ($variant->inventory_total === null) {
            return [
                'date' => '',
                'assigned' => $proposedCapacity,
                'limit' => 0,
                'reason' => 'missing_inventory_total',
            ];
        }

        $allocations = $this->activeHardAllocations((int) $variant->id, $excludeAllocationId);

        $proposed = new Allocation([
            'allocation_type' => Allocation::TYPE_HARD,
            'capacity' => $proposedCapacity,
            'start_date' => $proposedStartDate,
            'end_date' => $proposedEndDate,
            'active' => true,
        ]);

        $allocations->push($proposed);

        $scanStart = $this->openDate($proposedStartDate);
        $scanEnd = $this->openDate($proposedEndDate, false);

        $breakpointDates = $this->breakpointDates($allocations, $scanStart, $scanEnd);
        $overrides = $this->wholeDayOverridesForRange((int) $variant->id, $scanStart, $scanEnd);

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
            $limit = $this->inventoryLimitOnDay($variant, $day, $overrides);

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
     * @return Collection<int, Allocation>
     */
    private function activeHardAllocations(int $variantId, ?int $excludeAllocationId): Collection
    {
        $query = Allocation::query()
            ->where('service_variant_id', $variantId)
            ->where('active', true)
            ->where('allocation_type', Allocation::TYPE_HARD);

        if ($excludeAllocationId !== null) {
            $query->where('id', '!=', $excludeAllocationId);
        }

        return $query->get();
    }

    /**
     * @param  Collection<int, Allocation>  $allocations
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
     * @return Collection<string, ServiceVariantAvailabilityOverride>
     */
    private function wholeDayOverridesForRange(int $variantId, string $rangeStart, string $rangeEnd): Collection
    {
        return ServiceVariantAvailabilityOverride::query()
            ->where('service_variant_id', $variantId)
            ->whereNull('start_time')
            ->whereDate('date', '>=', $rangeStart)
            ->whereDate('date', '<=', $rangeEnd)
            ->get()
            ->keyBy(fn (ServiceVariantAvailabilityOverride $override): string => $override->date->format('Y-m-d'));
    }

    /**
     * @param  Collection<int, Allocation>  $allocations
     */
    private function assignedHardCapacityOnDay(Collection $allocations, string $day): int
    {
        $sum = 0;

        foreach ($allocations as $allocation) {
            if ($allocation->allocation_type !== Allocation::TYPE_HARD || ! $allocation->active) {
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
     * @param  Collection<string, ServiceVariantAvailabilityOverride>  $overridesByDate
     */
    private function inventoryLimitOnDay(
        ServiceVariant $variant,
        string $day,
        Collection $overridesByDate,
    ): ?int {
        if ($variant->inventory_type === 'unlimited') {
            return null;
        }

        $override = $overridesByDate->get($day);
        if ($override instanceof ServiceVariantAvailabilityOverride) {
            if ($override->closed) {
                return 0;
            }

            if ($override->capacity !== null) {
                return (int) $override->capacity;
            }
        }

        return $variant->inventory_total !== null ? (int) $variant->inventory_total : null;
    }

    private function openDate(?string $date, bool $isStart = true): string
    {
        if ($date !== null && $date !== '') {
            return $date;
        }

        return $isStart ? self::OPEN_START : self::OPEN_END;
    }
}
