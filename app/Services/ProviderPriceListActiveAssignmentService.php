<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PriceList;
use App\Models\PriceListAssignment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Resolves operator assignments on a provider price list that are active on a given date.
 */
final class ProviderPriceListActiveAssignmentService
{
    /**
     * @return Collection<int, PriceListAssignment>
     */
    public function activeAssignments(PriceList $priceList, ?Carbon $date = null): Collection
    {
        if (! $this->priceListIsActiveOnDate($priceList, $date)) {
            return collect();
        }

        return $priceList->assignments()
            ->where('is_active', true)
            ->with('operator')
            ->orderBy('id')
            ->get()
            ->filter(fn (PriceListAssignment $assignment): bool => $this->assignmentIsActiveOnDate($assignment, $date))
            ->values();
    }

    public function hasActiveOperatorAssignments(PriceList $priceList, ?Carbon $date = null): bool
    {
        return $this->activeAssignments($priceList, $date)->isNotEmpty();
    }

    /**
     * @return array<int, string> operator_account_id => display label
     */
    public function activeOperatorLabels(PriceList $priceList, ?Carbon $date = null): array
    {
        $labels = [];

        foreach ($this->activeAssignments($priceList, $date) as $assignment) {
            $operator = $assignment->operator;
            if ($operator === null) {
                continue;
            }

            $id = (int) $operator->id;
            $label = $operator->commercial_name ?? $operator->name ?? $operator->nick ?? ('#'.$id);
            $labels[$id] = (string) $label;
        }

        return $labels;
    }

    private function priceListIsActiveOnDate(PriceList $priceList, ?Carbon $date = null): bool
    {
        return $priceList->is_active;
    }

    private function assignmentIsActiveOnDate(PriceListAssignment $assignment, ?Carbon $date = null): bool
    {
        return $this->windowContains($assignment->valid_from, $assignment->valid_to, $this->resolveDate($date));
    }

    /**
     * @param  \DateTimeInterface|string|null  $from
     * @param  \DateTimeInterface|string|null  $to
     */
    private function windowContains($from, $to, Carbon $date): bool
    {
        if ($from) {
            $fromDay = Carbon::parse($from)->toDateString();
            if ($date->toDateString() < $fromDay) {
                return false;
            }
        }

        if ($to) {
            $toDay = Carbon::parse($to)->toDateString();
            if ($date->toDateString() > $toDay) {
                return false;
            }
        }

        return true;
    }

    private function resolveDate(?Carbon $date): Carbon
    {
        return ($date ?? now())->copy()->startOfDay();
    }
}
