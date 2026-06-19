<?php

namespace App\Services;

use App\Enums\CurrencyRateSide;
use App\Models\AccountType;
use App\Models\CommercialModulePrice;
use App\Models\Currency;
use App\Models\Module;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;

/**
 * Builds view data for the public pricing page from catalog modules and commercial prices.
 */
class PricingPageService
{
    public const BASE_MODULE_CODE = 'core';

    public function __construct(
        private readonly CurrencyConversionService $currencyConversion,
        private readonly ModulePricingCalculator $calculator,
    ) {}

    /**
     * @return array{pricingConfig: array<string, mixed>}
     */
    public function build(): array
    {
        $accountTypes = AccountType::query()
            ->where('active', true)
            ->ordered()
            ->with(['translations.language.locale'])
            ->get()
            ->sortBy(fn (AccountType $type): int => match ((string) $type->code) {
                'operator' => 1,
                'agency' => 2,
                'provider' => 3,
                default => 99,
            })
            ->values()
            ->map(fn (AccountType $type): array => [
                'code' => (string) $type->code,
                'name' => $type->name !== '' ? $type->name : (string) $type->code,
            ])
            ->values()
            ->all();

        $modules = Module::query()
            ->where('active', true)
            ->orderBy('sort_order')
            ->with([
                'translations.language.locale',
                'accountTypes',
                'features' => fn ($query) => $query->where('active', true)
                    ->orderBy('sort_order')
                    ->with(['translations.language.locale']),
                'commercialModulePrices' => fn ($query) => $query->where('active', true)
                    ->with(['tiers' => fn ($tierQuery) => $tierQuery->orderBy('from_users')]),
            ])
            ->get();

        $coreModule = $this->resolveBaseModule($modules);
        $defaultUserCount = $this->resolveDefaultUserCount();
        $currencyOptions = $this->buildCurrencyOptions();
        $userPresets = $this->buildUserPresets($coreModule, $defaultUserCount);
        $quoteUserCounts = $this->buildQuoteUserCounts($userPresets);

        return [
            'pricingConfig' => [
                'calcVersion' => 2,
                'accountTypes' => $accountTypes,
                'defaultAccountTypeCode' => $accountTypes[0]['code'] ?? 'operator',
                'defaultUserCount' => $defaultUserCount,
                'userPresets' => $userPresets,
                'quoteUserCounts' => $quoteUserCounts,
                'currencies' => $currencyOptions['currencies'],
                'defaultCurrencyId' => $currencyOptions['defaultCurrencyId'],
                'coreModuleId' => $coreModule?->id,
                'modules' => $modules
                    ->map(fn (Module $module): array => $this->moduleToPricingArray($module, $coreModule, $quoteUserCounts))
                    ->values()
                    ->all(),
                'labels' => $this->pricingLabels(),
            ],
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
        return $module->commercialModulePrices->first();
    }

    private function resolveDefaultUserCount(): int
    {
        return 1;
    }

    /**
     * @return list<int>
     */
    private function buildUserPresets(?Module $coreModule, int $defaultUserCount): array
    {
        $presets = [1, 5, 10, 20, $defaultUserCount];
        $price = $coreModule ? $this->resolvePrimaryPrice($coreModule) : null;

        if ($price !== null) {
            foreach ($price->tiers as $tier) {
                if ($tier->from_users !== null) {
                    $presets[] = (int) $tier->from_users;
                }
                if ($tier->to_users !== null) {
                    $presets[] = (int) $tier->to_users;
                }
            }
        }

        $presets = array_values(array_unique(array_filter($presets, fn (int $value): bool => $value > 0)));
        sort($presets);

        return $presets;
    }

    /**
     * User counts for which monthly amounts are pre-calculated server-side (source of truth).
     *
     * @param  list<int>  $userPresets
     * @return list<int>
     */
    private function buildQuoteUserCounts(array $userPresets): array
    {
        $counts = array_merge($userPresets, range(1, 50));

        $counts = array_values(array_unique(array_filter(
            $counts,
            fn (int $value): bool => $value > 0,
        )));
        sort($counts);

        return $counts;
    }

    /**
     * @param  list<int>  $quoteUserCounts
     * @return array<int, float>
     */
    private function buildAmountsByUsers(?CommercialModulePrice $price, array $quoteUserCounts): array
    {
        if ($price === null) {
            return [];
        }

        $amounts = [];
        foreach ($quoteUserCounts as $userCount) {
            $amount = $this->calculator->monthlyAmount($price, $userCount);
            if ($amount !== null) {
                $amounts[$userCount] = $amount;
            }
        }

        return $amounts;
    }

    /**
     * @return array{currencies: list<array<string, mixed>>, defaultCurrencyId: int|null}
     */
    private function buildCurrencyOptions(): array
    {
        $currencies = Currency::query()
            ->with('lmpCurrency')
            ->get()
            ->sortBy(fn (Currency $currency): string => Currency::isUsdProjectCurrency($currency->id)
                ? '0'
                : strtoupper($currency->currency_code))
            ->values();

        $options = [];
        $defaultCurrencyId = null;

        foreach ($currencies as $currency) {
            $isUsd = Currency::isUsdProjectCurrency($currency->id);
            $unitsPerUsd = $isUsd
                ? 1.0
                : $this->currencyConversion->unitsPerUsdAt(
                    (int) $currency->id,
                    CurrencyRateSide::Sell,
                );

            if (! $isUsd && $unitsPerUsd === null) {
                continue;
            }

            $rateRow = $isUsd
                ? null
                : $this->currencyConversion->effectiveRateRow((int) $currency->id);

            if ($isUsd) {
                $defaultCurrencyId = (int) $currency->id;
            }

            $symbol = trim((string) ($currency->lmpCurrency?->symbol ?? ''));
            $code = $currency->currency_code;
            $currencyName = trim((string) ($currency->lmpCurrency?->name ?? ''));

            $options[] = [
                'id' => (int) $currency->id,
                'code' => $code,
                'symbol' => $symbol !== '' ? $symbol : $code,
                'name' => $currency->display_name,
                'label' => $currencyName !== '' ? "{$code} - {$currencyName}" : $code,
                'isUsd' => $isUsd,
                'unitsPerUsd' => $unitsPerUsd,
                'rateDate' => $rateRow?->starting_at?->toDateString(),
                'rateDateLabel' => $rateRow?->starting_at !== null
                    ? locale_date($rateRow->starting_at)
                    : null,
            ];
        }

        if ($defaultCurrencyId === null && $options !== []) {
            $defaultCurrencyId = (int) $options[0]['id'];
        }

        return [
            'currencies' => $options,
            'defaultCurrencyId' => $defaultCurrencyId,
        ];
    }

    private function moduleToPricingArray(Module $module, ?Module $coreModule, array $quoteUserCounts): array
    {
        $price = $this->resolvePrimaryPrice($module);

        return [
            'id' => $module->id,
            'code' => $module->code,
            'isCore' => $coreModule !== null && $module->is($coreModule),
            'name' => $module->name,
            'description' => $module->description,
            'accountTypeCodes' => $module->accountTypes
                ->pluck('code')
                ->map(fn ($code): string => (string) $code)
                ->values()
                ->all(),
            'billingType' => $price?->billing_type,
            'basePrice' => $price?->base_price !== null ? (float) $price->base_price : null,
            'includedUsers' => $price?->included_users !== null ? (int) $price->included_users : null,
            'pricePerUser' => $price?->price_per_user !== null ? (float) $price->price_per_user : null,
            'amountsByUsers' => $this->buildAmountsByUsers($price, $quoteUserCounts),
            'tiers' => $price
                ? $price->tiers
                    ->map(fn ($tier): array => [
                        'fromUsers' => $tier->from_users,
                        'toUsers' => $tier->to_users,
                        'pricePerUser' => $tier->price_per_user !== null ? (float) $tier->price_per_user : null,
                    ])
                    ->values()
                    ->all()
                : [],
            'features' => $module->features
                ->map(fn ($feature): ?string => $feature->text)
                ->filter(fn (?string $text): bool => $text !== null && $text !== '')
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function pricingLabels(): array
    {
        return [
            'currency' => (string) __('pricing.currency'),
            'perMonth' => (string) __('pricing.per_month'),
            'customQuote' => (string) __('pricing.custom_quote'),
            'stepAccountType' => (string) __('pricing.step_account_type'),
            'stepAccountTypeHelp' => (string) __('pricing.step_account_type_help'),
            'stepUsers' => (string) __('pricing.step_users'),
            'stepUsersHelp' => (string) __('pricing.step_users_help'),
            'stepCurrency' => (string) __('pricing.step_currency'),
            'stepCurrencyHelp' => (string) __('pricing.step_currency_help'),
            'usersLabel' => (string) __('pricing.users_label'),
            'coreHeading' => (string) __('pricing.core_heading'),
            'coreIntro' => (string) __('pricing.core_intro'),
            'coreRequired' => (string) __('pricing.core_required'),
            'addonsHeading' => (string) __('pricing.addons_heading'),
            'addonsIntro' => (string) __('pricing.addons_intro'),
            'selectModule' => (string) __('pricing.select_module'),
            'estimateHeading' => (string) __('pricing.estimate_heading'),
            'estimateIntro' => (string) __('pricing.estimate_intro'),
            'estimateCore' => (string) __('pricing.estimate_core'),
            'estimateAddons' => (string) __('pricing.estimate_addons'),
            'estimateTotal' => (string) __('pricing.estimate_total'),
            'estimateEmpty' => (string) __('pricing.estimate_empty'),
            'noModulesForType' => (string) __('pricing.no_modules_for_type'),
            'billingFixed' => (string) __('pricing.billing_fixed'),
            'billingPerUser' => (string) __('pricing.billing_per_user'),
            'billingPerUserAmount' => (string) __('pricing.billing_per_user_amount'),
            'billingPerUserBaseAndAmount' => (string) __('pricing.billing_per_user_base_and_amount'),
            'billingHybrid' => (string) __('pricing.billing_hybrid'),
            'billingUsage' => (string) __('pricing.billing_usage'),
            'usersContext' => (string) __('pricing.users_context'),
            'pricesUsdNote' => (string) __('pricing.prices_usd_note'),
            'exchangeRateNote' => (string) __('pricing.exchange_rate_note'),
            'block1Highlight' => (string) __('pricing.block1_highlight'),
            'signUpNow' => (string) __('pricing.sign_up_now'),
            'estimateContext' => (string) __('pricing.estimate_context'),
            'usersSummary' => (string) __('pricing.users_summary'),
        ];
    }

    /**
     * Format a price for display using the current locale (comma or period as decimal).
     */
    public function formatPriceForLocale(mixed $value): string
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
