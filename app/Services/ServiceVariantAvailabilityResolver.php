<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ServiceVariant;
use App\Models\ServiceVariantAvailabilityOverride;
use App\Models\ServiceVariantAvailabilityRule;
use App\Models\ServiceVariantAvailabilityTimeSlot;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Resolves effective availability and capacity for a service variant on a given date.
 *
 * @see docs/service-availability-model.md
 */
final class ServiceVariantAvailabilityResolver
{
    public function __construct(
        private readonly ServiceAvailabilityResolver $serviceAvailabilityResolver,
    ) {
    }

    public function resolveForDate(
        ServiceVariant $variant,
        Carbon $date,
        ?string $slotStartTime = null,
    ): AvailabilityResolution {
        $date = $date->copy()->startOfDay();

        $variant->loadMissing([
            'service.availabilityRules',
            'service.availabilityOverrides',
        ]);

        $service = $variant->service;
        if ($service !== null) {
            $serviceResolution = $this->serviceAvailabilityResolver->resolveForDate($service, $date);
            if (! $serviceResolution->available) {
                return $serviceResolution;
            }
        }

        if (! $this->hasMatchingRule($variant, $date)) {
            return new AvailabilityResolution(available: false, capacity: 0);
        }

        return match ($variant->inventory_type) {
            'unlimited' => new AvailabilityResolution(available: true, capacity: null),
            'per_day' => $this->resolvePerDay($variant, $date),
            'per_timeslot', 'per_departure' => $this->resolvePerTimeSlot($variant, $date, $slotStartTime),
            default => new AvailabilityResolution(available: false, capacity: 0),
        };
    }

    private function hasMatchingRule(ServiceVariant $variant, Carbon $date): bool
    {
        $rules = $this->rulesForVariant($variant);

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

    private function resolvePerDay(ServiceVariant $variant, Carbon $date): AvailabilityResolution
    {
        $override = $this->wholeDayOverride($variant, $date);

        if ($override instanceof ServiceVariantAvailabilityOverride) {
            if ($override->closed) {
                return new AvailabilityResolution(available: false, capacity: 0, closed: true);
            }

            if ($override->capacity !== null) {
                return new AvailabilityResolution(available: true, capacity: (int) $override->capacity);
            }
        }

        if ($variant->inventory_total === null) {
            return new AvailabilityResolution(available: false, capacity: 0);
        }

        return new AvailabilityResolution(available: true, capacity: (int) $variant->inventory_total);
    }

    private function resolvePerTimeSlot(
        ServiceVariant $variant,
        Carbon $date,
        ?string $slotStartTime,
    ): AvailabilityResolution {
        $rules = $this->rulesForVariant($variant)->filter(
            fn (ServiceVariantAvailabilityRule $rule): bool => $this->ruleMatchesDate($rule, $date),
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

                    return $this->resolveSlotCapacity($variant, $date, $slot);
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
                $resolution = $this->resolveSlotCapacity($variant, $date, $slot);
                if (! $resolution->available) {
                    continue;
                }

                $maxCapacity = max($maxCapacity, (int) ($resolution->capacity ?? 0));
            }
        }

        if (! $anySlot) {
            return new AvailabilityResolution(available: false, capacity: 0);
        }

        if ($maxCapacity <= 0) {
            return new AvailabilityResolution(available: false, capacity: 0);
        }

        return new AvailabilityResolution(available: true, capacity: $maxCapacity);
    }

    private function resolveSlotCapacity(
        ServiceVariant $variant,
        Carbon $date,
        ServiceVariantAvailabilityTimeSlot $slot,
    ): AvailabilityResolution {
        $override = $this->slotOverride($variant, $date, $slot->start_time);

        if ($override instanceof ServiceVariantAvailabilityOverride) {
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

        if ($variant->inventory_total === null) {
            return new AvailabilityResolution(available: false, capacity: 0);
        }

        return new AvailabilityResolution(available: true, capacity: (int) $variant->inventory_total);
    }

    /**
     * @return Collection<int, ServiceVariantAvailabilityRule>
     */
    private function rulesForVariant(ServiceVariant $variant): Collection
    {
        if ($variant->relationLoaded('availabilityRules')) {
            $variant->loadMissing('availabilityRules.timeSlots');

            return $variant->availabilityRules
                ->where('active', true)
                ->values();
        }

        return $variant->availabilityRules()
            ->with('timeSlots')
            ->where('active', true)
            ->get();
    }

    private function ruleMatchesDate(ServiceVariantAvailabilityRule $rule, Carbon $date): bool
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

    private function wholeDayOverride(ServiceVariant $variant, Carbon $date): ?ServiceVariantAvailabilityOverride
    {
        $dateKey = $date->toDateString();

        if ($variant->relationLoaded('availabilityOverrides')) {
            return $variant->availabilityOverrides
                ->first(fn (ServiceVariantAvailabilityOverride $row): bool => $row->date?->toDateString() === $dateKey
                    && $row->start_time === null);
        }

        return ServiceVariantAvailabilityOverride::query()
            ->where('service_variant_id', $variant->id)
            ->whereDate('date', $dateKey)
            ->whereNull('start_time')
            ->first();
    }

    private function slotOverride(
        ServiceVariant $variant,
        Carbon $date,
        mixed $slotStartTime,
    ): ?ServiceVariantAvailabilityOverride {
        $dateKey = $date->toDateString();
        $timeKey = $this->normalizeTime($slotStartTime);

        if ($variant->relationLoaded('availabilityOverrides')) {
            return $variant->availabilityOverrides->first(
                fn (ServiceVariantAvailabilityOverride $row): bool => $row->date?->toDateString() === $dateKey
                    && $this->normalizeTime($row->start_time) === $timeKey,
            );
        }

        return ServiceVariantAvailabilityOverride::query()
            ->where('service_variant_id', $variant->id)
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
