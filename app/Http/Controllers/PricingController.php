<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\PlanUserPrice;
use Illuminate\Support\Number;
use Illuminate\View\View;

class PricingController extends Controller
{
    /**
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
     * Module price for a given user range. Override later with real formula.
     * For now returns the plan's base price regardless of range.
     */
    public function getModulePriceForUserRange(Plan $plan, int $upTo): string
    {
        $price = $plan->price;

        return $price !== null && $price !== '' ? (string) $price : '';
    }

    /**
     * Display the pricing page with active service plans and their items.
     */
    public function __invoke(): View
    {
        $plans = Plan::query()
            ->where('active', true)
            ->orderBy('sort_order')
            ->with([
                'translations.language',
                'items' => fn ($q) => $q->where('active', true)->orderBy('sort_order')
                    ->with(['translations.language', 'children' => fn ($q) => $q->where('active', true)->orderBy('sort_order')->with('translations.language')]),
            ])
            ->get();

        $starterPlan = $plans->firstWhere('code', 'starter');
        $modulePlans = $plans->where('code', '!=', 'starter')->values();

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

        $modulePricesByRange = [];
        foreach ($modulePlans as $plan) {
            $modulePricesByRange[$plan->id] = [];
            foreach ($rangeTabs as $tab) {
                $raw = $this->getModulePriceForUserRange($plan, $tab['up_to']);
                $modulePricesByRange[$plan->id][$tab['up_to']] = $this->formatPriceForLocale($raw);
            }
        }

        return view('pages.pricing', compact(
            'plans',
            'starterPlan',
            'modulePlans',
            'rangeTabs',
            'defaultUpTo',
            'modulePricesByRange'
        ));
    }
}
