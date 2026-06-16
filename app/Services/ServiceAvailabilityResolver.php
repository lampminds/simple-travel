<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceAvailabilityOverride;
use App\Models\ServiceAvailabilityRule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Resolves service-level open/closed state before variant availability is evaluated.
 *
 * @see docs/service-availability-model.md
 */
final class ServiceAvailabilityResolver
{
    public function resolveForDate(Service $service, Carbon $date): AvailabilityResolution
    {
        $date = $date->copy()->startOfDay();

        if ($this->isClosedByOverride($service, $date)) {
            return new AvailabilityResolution(available: false, capacity: 0, closed: true);
        }

        $rules = $this->rulesForService($service);
        if ($rules->isNotEmpty() && ! $this->hasMatchingRule($rules, $date)) {
            return new AvailabilityResolution(available: false, capacity: 0);
        }

        return new AvailabilityResolution(available: true, capacity: null);
    }

    public function isClosedByOverride(Service $service, Carbon $date): bool
    {
        foreach ($this->overridesForService($service) as $override) {
            if (! $override->closed) {
                continue;
            }

            if ($override->coversDate($date)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection<int, ServiceAvailabilityRule>
     */
    private function rulesForService(Service $service): Collection
    {
        if ($service->relationLoaded('availabilityRules')) {
            return $service->availabilityRules
                ->where('active', true)
                ->values();
        }

        return $service->availabilityRules()
            ->where('active', true)
            ->get();
    }

    /**
     * @return Collection<int, ServiceAvailabilityOverride>
     */
    private function overridesForService(Service $service): Collection
    {
        if ($service->relationLoaded('availabilityOverrides')) {
            return $service->availabilityOverrides->values();
        }

        return $service->availabilityOverrides()->get();
    }

    /**
     * @param  Collection<int, ServiceAvailabilityRule>  $rules
     */
    private function hasMatchingRule(Collection $rules, Carbon $date): bool
    {
        foreach ($rules as $rule) {
            if ($this->ruleMatchesDate($rule, $date)) {
                return true;
            }
        }

        return false;
    }

    private function ruleMatchesDate(ServiceAvailabilityRule $rule, Carbon $date): bool
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
}
