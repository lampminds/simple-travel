<?php

namespace App\Http\Controllers;

use App\Models\PlanUserPrice;
use Illuminate\Support\Number;
use Illuminate\View\View;

class PricingController extends Controller
{    /**
     * Format a price for display using the current locale (comma or period as decimal).
     * Non-numeric values (e.g. "150mil") are returned as-is.
     */
    private function formatPriceForLocale(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        $numeric = is_numeric($value) ? (float) $value : null;

        return $numeric !== null
            ? Number::format($numeric, 2, null, app()->getLocale())
            : (string) $value;
    }

    /**
     * Display the pricing page with active service plans and their items.
     */
    public function __invoke(): View
    {
        /** @var \Illuminate\Support\Collection<int, mixed> $plans Legacy variable retained for the Blade contract; commercial plans wiring comes later. */
        $plans = collect();
        $starterPlan = null;
        /** @var \Illuminate\Support\Collection<int, mixed> $modulePlans */
        $modulePlans = collect();
        $modulePricesByRange = [];

        $userRanges = PlanUserPrice::query()
            ->orderBy('up_to')
            ->with(['translations.language.locale'])
            ->get();

        $rangeTabs = [];
        $prevUpTo = 0;
        foreach ($userRanges as $range) {
            $from = $prevUpTo + 1;
            $to = $range->up_to;
            $rangeTabs[] = [
                'up_to' => $range->up_to,
                'label' => $from === $to
                    ? __("pricing.range_label_up_to", ['count' => $to])
                    : __("pricing.range_label_from_to", ['from' => $from, 'to' => $to]),
                'base_price' => $this->formatPriceForLocale($range->price),
            ];
            $prevUpTo = $range->up_to;
        }

        $defaultUpTo = $rangeTabs[0]['up_to'] ?? null;

        return view('pages.pricing', compact(
            'plans',
            'starterPlan',
            'modulePlans',
            'rangeTabs',
            'defaultUpTo',
            'modulePricesByRange',
        ));
    }
}
