<?php

namespace App\Services;

use App\Models\CommercialModulePrice;
use App\Models\CommercialModulePriceTier;
use App\Models\Module;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;

/**
 * Builds view data for the public pricing page from catalog modules and commercial prices.
 */
class PricingPageService
{
    public const BASE_MODULE_CODE = 'core';

    /**
     * @return array{
     *     plans: Collection<int, mixed>,
     *     starterPlan: object|null,
     *     modulePlans: Collection<int, object>,
     *     rangeTabs: list<array{up_to: int, label: string, base_price: string}>,
     *     defaultUpTo: int|null,
     *     modulePricesByRange: array<int, array<int, string>>,
     * }
     */
    public function build(): array
    {
        $modules = Module::query()
            ->where('active', true)
            ->orderBy('sort_order')
            ->with([
                'translations.language.locale',
                'features' => fn ($query) => $query->where('active', true)
                    ->orderBy('sort_order')
                    ->with(['translations.language.locale']),
                'commercialModulePrices' => fn ($query) => $query->where('active', true)
                    ->with(['tiers' => fn ($tierQuery) => $tierQuery->orderBy('from_users')]),
            ])
            ->get();

        $baseModule = $this->resolveBaseModule($modules);
        $basePrice = $baseModule ? $this->resolvePrimaryPrice($baseModule) : null;
        $rangeTabs = $this->buildRangeTabs($basePrice);
        $defaultUpTo = $rangeTabs[0]['up_to'] ?? null;

        $starterPlan = $baseModule !== null
            ? $this->presentModule(
                $baseModule,
                $defaultUpTo !== null && $basePrice !== null
                    ? $this->calculateMonthlyPrice($basePrice, $defaultUpTo)
                    : $this->defaultListPrice($basePrice),
            )
            : null;

        $modulePlans = $modules
            ->reject(fn (Module $module): bool => $baseModule !== null && $module->is($baseModule))
            ->values()
            ->map(function (Module $module) use ($rangeTabs, $defaultUpTo): object {
                $price = $this->resolvePrimaryPrice($module);
                $amount = $defaultUpTo !== null && $rangeTabs !== []
                    ? $this->calculateMonthlyPrice($price, $defaultUpTo)
                    : $this->defaultListPrice($price);

                return $this->presentModule($module, $amount);
            });

        $modulePricesByRange = [];
        foreach ($modulePlans as $modulePlan) {
            $module = $modules->firstWhere('id', $modulePlan->id);
            if ($module === null) {
                continue;
            }
            $price = $this->resolvePrimaryPrice($module);
            $modulePricesByRange[$modulePlan->id] = [];
            foreach ($rangeTabs as $tab) {
                $raw = $this->calculateMonthlyPrice($price, $tab['up_to']);
                $modulePricesByRange[$modulePlan->id][$tab['up_to']] = $this->formatPriceForLocale($raw);
            }
        }

        return [
            'plans' => collect(),
            'starterPlan' => $starterPlan,
            'modulePlans' => $modulePlans,
            'rangeTabs' => $rangeTabs,
            'defaultUpTo' => $defaultUpTo,
            'modulePricesByRange' => $modulePricesByRange,
        ];
    }

    /**
     * @param  Collection<int, Module>  $modules
     */
    private function resolveBaseModule(Collection $modules): ?Module
    {
        $byCode = $modules->firstWhere('code', self::BASE_MODULE_CODE)
            ?? $modules->firstWhere('code', 'starter');

        if ($byCode !== null) {
            return $byCode;
        }

        return $modules->first(
            fn (Module $module): bool => $this->resolvePrimaryPrice($module)?->billing_type === 'hybrid',
        );
    }

    private function resolvePrimaryPrice(Module $module): ?CommercialModulePrice
    {
        $prices = $module->commercialModulePrices;

        $preferredType = match ($module->code) {
            self::BASE_MODULE_CODE, 'starter' => 'hybrid',
            'crm' => 'per_user',
            'website' => 'fixed',
            'api' => 'usage',
            default => null,
        };

        if ($preferredType !== null) {
            $match = $prices->firstWhere('billing_type', $preferredType);
            if ($match !== null) {
                return $match;
            }
        }

        return $prices->first();
    }

    /**
     * @return list<array{up_to: int, label: string, base_price: string}>
     */
    private function buildRangeTabs(?CommercialModulePrice $basePrice): array
    {
        if ($basePrice === null) {
            return [];
        }

        if ($basePrice->tiers->isNotEmpty()) {
            return $this->buildRangeTabsFromTiers($basePrice);
        }

        if (! in_array($basePrice->billing_type, ['hybrid', 'per_user'], true)) {
            return [];
        }

        $amount = $this->defaultListPrice($basePrice);
        if ($amount === null) {
            return [];
        }

        $includedUsers = max(1, (int) ($basePrice->included_users ?? 1));

        return [[
            'up_to' => $includedUsers,
            'label' => __('pricing.range_label_up_to', ['count' => $includedUsers]),
            'base_price' => $this->formatPriceForLocale($amount),
        ]];
    }

    /**
     * @return list<array{up_to: int, label: string, base_price: string}>
     */
    private function buildRangeTabsFromTiers(CommercialModulePrice $basePrice): array
    {
        $tabs = [];
        $prevUpTo = 0;

        foreach ($basePrice->tiers as $tier) {
            $from = $tier->from_users ?? ($prevUpTo + 1);
            $to = $tier->to_users;
            $representativeUsers = $to ?? max($from, 1);

            $label = $to === null
                ? __('pricing.block1_range_20_plus')
                : ($from === $to
                    ? __('pricing.range_label_up_to', ['count' => $to])
                    : __('pricing.range_label_from_to', ['from' => $from, 'to' => $to]));

            $amount = $to === null
                ? null
                : $this->calculateMonthlyPrice($basePrice, $representativeUsers);

            $tabs[] = [
                'up_to' => $representativeUsers,
                'label' => $label,
                'base_price' => $amount === null
                    ? (string) __('pricing.block1_range_20_plus_custom')
                    : $this->formatPriceForLocale($amount),
            ];

            $prevUpTo = $to ?? $prevUpTo;
        }

        return $tabs;
    }

    private function calculateMonthlyPrice(?CommercialModulePrice $price, int $userCount): ?float
    {
        if ($price === null) {
            return null;
        }

        return match ($price->billing_type) {
            'fixed', 'usage' => $price->base_price !== null ? (float) $price->base_price : null,
            'per_user' => $this->calculatePerUserPrice($price, $userCount),
            'hybrid' => $this->calculateHybridPrice($price, $userCount),
            default => null,
        };
    }

    private function calculatePerUserPrice(CommercialModulePrice $price, int $userCount): ?float
    {
        $perUser = $this->resolvePerUserRate($price, $userCount);
        if ($perUser === null) {
            return null;
        }

        return (float) $perUser * $userCount;
    }

    private function calculateHybridPrice(CommercialModulePrice $price, int $userCount): ?float
    {
        $base = (float) ($price->base_price ?? 0);
        $includedUsers = (int) ($price->included_users ?? 0);
        $extraUsers = max(0, $userCount - $includedUsers);

        if ($extraUsers === 0) {
            return $base > 0 || $price->base_price !== null ? $base : null;
        }

        $perUser = $this->resolvePerUserRate($price, $userCount);
        if ($perUser === null) {
            return $base > 0 || $price->base_price !== null ? $base : null;
        }

        return $base + ($extraUsers * (float) $perUser);
    }

    private function resolvePerUserRate(CommercialModulePrice $price, int $userCount): ?float
    {
        $tier = $this->findTierForUserCount($price, $userCount);
        $perUser = $tier?->price_per_user ?? $price->price_per_user;

        return $perUser !== null ? (float) $perUser : null;
    }

    private function findTierForUserCount(CommercialModulePrice $price, int $userCount): ?CommercialModulePriceTier
    {
        foreach ($price->tiers as $tier) {
            $from = $tier->from_users ?? 1;
            $to = $tier->to_users;

            if ($userCount >= $from && ($to === null || $userCount <= $to)) {
                return $tier;
            }
        }

        return null;
    }

    private function defaultListPrice(?CommercialModulePrice $price): ?float
    {
        if ($price === null) {
            return null;
        }

        if ($price->billing_type === 'fixed' || $price->billing_type === 'usage') {
            return $price->base_price !== null ? (float) $price->base_price : null;
        }

        $firstTierUsers = $price->tiers->first()?->to_users
            ?? $price->tiers->first()?->from_users
            ?? max(1, (int) ($price->included_users ?? 1));

        return $this->calculateMonthlyPrice($price, (int) $firstTierUsers);
    }

    private function presentModule(Module $module, ?float $amount = null): object
    {
        $items = $module->features
            ->map(fn ($feature): object => (object) [
                'display_text' => $feature->text,
                'children' => collect(),
            ])
            ->filter(fn (object $item): bool => $item->display_text !== null && $item->display_text !== '')
            ->values();

        return (object) [
            'id' => $module->id,
            'name' => $module->name,
            'description' => $module->description,
            'price' => $this->formatPriceForLocale($amount),
            'items' => $items,
        ];
    }

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
}
