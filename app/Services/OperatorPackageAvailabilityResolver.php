<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OperatorPackageAvailabilityOverride;
use App\Models\OperatorPackageAvailabilityRule;
use App\Models\OperatorPackageAvailabilityTimeSlot;
use App\Models\OperatorServiceCatalog;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Resolves effective availability and capacity for an operator package on a given date.
 *
 * @see docs/service-availability-model.md (same layering as service variants)
 */
final class OperatorPackageAvailabilityResolver
{
    public function resolveForDate(
        OperatorServiceCatalog $catalog,
        Carbon $date,
        ?string $slotStartTime = null,
    ): AvailabilityResolution {
        $date = $date->copy()->startOfDay();

        if (! $this->hasMatchingRule($catalog, $date)) {
            return new AvailabilityResolution(available: false, capacity: 0);
        }

        $inventoryType = $catalog->inventory_type ?? 'unlimited';

        return match ($inventoryType) {
            'unlimited' => new AvailabilityResolution(available: true, capacity: null),
            'per_day' => $this->resolvePerDay($catalog, $date),
            'per_timeslot', 'per_departure' => $this->resolvePerTimeSlot($catalog, $date, $slotStartTime),
            default => new AvailabilityResolution(available: false, capacity: 0),
        };
    }

    private function hasMatchingRule(OperatorServiceCatalog $catalog, Carbon $date): bool
    {
        $rules = $this->rulesForCatalog($catalog);

        if ($rules->isEmpty()) {
            return false;
        }

        foreach ($rules as $rule) {
            if ($this->ruleMatchesDate($rule, $date)) {
                return true;
            }
        }

        return false;
    }

    private function resolvePerDay(OperatorServiceCatalog $catalog, Carbon $date): AvailabilityResolution
    {
        $override = $this->wholeDayOverride($catalog, $date);

        if ($override instanceof OperatorPackageAvailabilityOverride) {
            if ($override->closed) {
                return new AvailabilityResolution(available: false, capacity: 0, closed: true);
            }

            if ($override->capacity !== null) {
                return new AvailabilityResolution(available: true, capacity: (int) $override->capacity);
            }
        }

        if ($catalog->inventory_total === null) {
            return new AvailabilityResolution(available: false, capacity: 0);
        }

        return new AvailabilityResolution(available: true, capacity: (int) $catalog->inventory_total);
    }

    private function resolvePerTimeSlot(
        OperatorServiceCatalog $catalog,
        Carbon $date,
        ?string $slotStartTime,
    ): AvailabilityResolution {
        $rules = $this->rulesForCatalog($catalog)->filter(
            fn (OperatorPackageAvailabilityRule $rule): bool => $this->ruleMatchesDate($rule, $date),
        );

        if ($slotStartTime !== null && $slotStartTime !== '') {
            $normalizedTime = $this->normalizeTime($slotStartTime);

            foreach ($rules as $rule) {
                foreach ($rule->timeSlots as $slot) {
                    if (! $slot->active) {
                        continue;
                    }

                    if ($this->normalizeTime($slot->start_time) !== $normalizedTime) {
                        continue;
                    }

                    return $this->resolveSlotCapacity($catalog, $date, $slot);
                }
            }

            return new AvailabilityResolution(available: false, capacity: 0);
        }

        $maxCapacity = 0;
        $anySlot = false;

        foreach ($rules as $rule) {
            foreach ($rule->timeSlots as $slot) {
                if (! $slot->active) {
                    continue;
                }

                $anySlot = true;
                $resolution = $this->resolveSlotCapacity($catalog, $date, $slot);
                if (! $resolution->available) {
                    continue;
                }

                $maxCapacity = max($maxCapacity, (int) ($resolution->capacity ?? 0));
            }
        }

        if (! $anySlot || $maxCapacity <= 0) {
            return new AvailabilityResolution(available: false, capacity: 0);
        }

        return new AvailabilityResolution(available: true, capacity: $maxCapacity);
    }

    private function resolveSlotCapacity(
        OperatorServiceCatalog $catalog,
        Carbon $date,
        OperatorPackageAvailabilityTimeSlot $slot,
    ): AvailabilityResolution {
        $override = $this->slotOverride($catalog, $date, $slot->start_time);

        if ($override instanceof OperatorPackageAvailabilityOverride) {
            if ($override->closed) {
                return new AvailabilityResolution(available: false, capacity: 0, closed: true);
            }

            if ($override->capacity !== null) {
                return new AvailabilityResolution(available: true, capacity: (int) $override->capacity);
            }
        }

        if ($slot->capacity !== null) {
            return new AvailabilityResolution(available: true, capacity: (int) $slot->capacity);
        }

        if ($catalog->inventory_total === null) {
            return new AvailabilityResolution(available: false, capacity: 0);
        }

        return new AvailabilityResolution(available: true, capacity: (int) $catalog->inventory_total);
    }

    /**
     * @return Collection<int, OperatorPackageAvailabilityRule>
     */
    private function rulesForCatalog(OperatorServiceCatalog $catalog): Collection
    {
        if ($catalog->relationLoaded('availabilityRules')) {
            $catalog->loadMissing('availabilityRules.timeSlots');

            return $catalog->availabilityRules
                ->where('active', true)
                ->values();
        }

        return $catalog->availabilityRules()
            ->with('timeSlots')
            ->where('active', true)
            ->get();
    }

    private function ruleMatchesDate(OperatorPackageAvailabilityRule $rule, Carbon $date): bool
    {
        if (! $rule->active) {
            return false;
        }

        if ($rule->start_date !== null && $date->lt($rule->start_date->copy()->startOfDay())) {
            return false;
        }

        if ($rule->end_date !== null && $date->gt($rule->end_date->copy()->startOfDay())) {
            return false;
        }

        if ($rule->weekday_mask !== null && $rule->weekday_mask > 0) {
            $bit = 1 << ($date->dayOfWeekIso - 1);
            if (($rule->weekday_mask & $bit) !== $bit) {
                return false;
            }
        }

        return true;
    }

    private function wholeDayOverride(OperatorServiceCatalog $catalog, Carbon $date): ?OperatorPackageAvailabilityOverride
    {
        $dateKey = $date->toDateString();

        if ($catalog->relationLoaded('availabilityOverrides')) {
            return $catalog->availabilityOverrides
                ->first(fn (OperatorPackageAvailabilityOverride $row): bool => $row->date?->toDateString() === $dateKey
                    && $row->start_time === null);
        }

        return OperatorPackageAvailabilityOverride::query()
            ->where('operator_service_catalog_id', $catalog->id)
            ->whereDate('date', $dateKey)
            ->whereNull('start_time')
            ->first();
    }

    private function slotOverride(
        OperatorServiceCatalog $catalog,
        Carbon $date,
        mixed $slotStartTime,
    ): ?OperatorPackageAvailabilityOverride {
        $dateKey = $date->toDateString();
        $timeKey = $this->normalizeTime($slotStartTime);

        if ($catalog->relationLoaded('availabilityOverrides')) {
            return $catalog->availabilityOverrides->first(
                fn (OperatorPackageAvailabilityOverride $row): bool => $row->date?->toDateString() === $dateKey
                    && $this->normalizeTime($row->start_time) === $timeKey,
            );
        }

        return OperatorPackageAvailabilityOverride::query()
            ->where('operator_service_catalog_id', $catalog->id)
            ->whereDate('date', $dateKey)
            ->where('start_time', $timeKey)
            ->first();
    }

    private function normalizeTime(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('H:i:s');
        }

        $string = trim((string) $value);
        if (strlen($string) === 5) {
            return $string.':00';
        }

        return $string;
    }
}
