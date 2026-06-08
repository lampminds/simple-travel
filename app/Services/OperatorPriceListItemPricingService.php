<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CurrencyRateSide;
use App\Models\Currency;
use App\Models\OperatorPackageItem;
use App\Models\ServiceVariant;
use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Resolves live operator list line pricing: provider cost (via assigned list) → FX → operator adjustment.
 */
final class OperatorPriceListItemPricingService
{
    public const MODE_PERCENTAGE = 'percentage';

    public const MODE_FIXED_DELTA = 'fixed_delta';

    public const MODE_FIXED_PRICE = 'fixed_price';

    public function __construct(
        private readonly OperatorVariantPriceResolver $variantPriceResolver,
        private readonly CurrencyConversionService $currencyConversion,
        private readonly PriceFormatService $priceFormatService,
    ) {
    }

    /**
     * @return array{
     *     provider_cost: float|null,
     *     provider_cost_has: bool,
     *     provider_cost_formatted: string,
     *     final_price: float|null,
     *     final_price_has: bool,
     *     final_price_formatted: string,
     *     provider_cost_available: bool,
     *     warning: string|null,
     *     breakdown_html: string|null,
     *     allowed_modes: list<string>
     * }
     */
    public function calculate(
        OperatorPackageItem $packageItem,
        int $operatorAccountId,
        int $listCurrencyId,
        ?string $pricingMode,
        float $price,
        ?CarbonInterface $pricingDate = null,
    ): array {
        $pricingDate ??= Carbon::today();
        $pricingMode = $this->normalizeMode($pricingMode);

        $variant = $packageItem->serviceVariant;
        $offer = $packageItem->serviceOffer;
        $providerId = $offer !== null ? (int) $offer->provider_id : 0;

        $listCurrency = Currency::query()->with('lmpCurrency')->find($listCurrencyId);
        $listCurrencyCode = $listCurrency?->currency_code;

        $providerUnitCost = null;
        $breakdownHtml = null;
        $warning = null;

        if ($variant instanceof ServiceVariant && $providerId > 0) {
            $resolved = $this->variantPriceResolver->resolve(
                $variant,
                $providerId,
                $operatorAccountId,
                $pricingDate,
            );
            $breakdownHtml = $resolved['breakdown_html'] ?? null;

            if ($resolved['has_amount'] && $resolved['amount'] !== null) {
                $sourceCurrencyId = isset($resolved['currency_id'])
                    ? (int) $resolved['currency_id']
                    : (int) $variant->currency_id;

                if ($sourceCurrencyId === $listCurrencyId) {
                    $providerUnitCost = (float) $resolved['amount'];
                } else {
                    // Provider list currency → operator list currency: conservative (higher cost in list currency).
                    $converted = $this->currencyConversion->convert(
                        (float) $resolved['amount'],
                        $sourceCurrencyId,
                        $listCurrencyId,
                        CurrencyRateSide::Buy,
                        CurrencyRateSide::Sell,
                        $operatorAccountId,
                        $pricingDate,
                    );
                    if ($converted === null) {
                        $warning = __('account.operator_price_lists.warnings.currency_conversion_failed');
                    } else {
                        $providerUnitCost = $converted;
                    }
                }
            }
        } elseif ($variant === null) {
            $warning = __('account.operator_price_lists.warnings.variant_required');
        }

        $providerCostAvailable = $providerUnitCost !== null;

        if (! $providerCostAvailable && $warning === null) {
            $warning = __('account.operator_price_lists.warnings.no_provider_cost');
        }

        if ($pricingMode === null) {
            $finalPrice = $providerUnitCost;
        } else {
            $finalPrice = $this->applyOperatorAdjustment($pricingMode, $price, $providerUnitCost);

            if (! $providerCostAvailable && $pricingMode !== self::MODE_FIXED_PRICE) {
                $finalPrice = null;
            }
        }

        return [
            'provider_cost' => $providerUnitCost,
            'provider_cost_has' => $providerUnitCost !== null,
            'provider_cost_formatted' => $providerUnitCost !== null
                ? $this->priceFormatService->formatWithCurrency($providerUnitCost, currencyCode: $listCurrencyCode, accountId: $operatorAccountId)
                : '—',
            'final_price' => $finalPrice,
            'final_price_has' => $finalPrice !== null,
            'final_price_formatted' => $finalPrice !== null
                ? $this->priceFormatService->formatWithCurrency($finalPrice, currencyCode: $listCurrencyCode, accountId: $operatorAccountId)
                : '—',
            'provider_cost_available' => $providerCostAvailable,
            'warning' => $warning,
            'breakdown_html' => $breakdownHtml,
            'allowed_modes' => $this->allowedModesForProviderCost($providerCostAvailable),
        ];
    }

    public function normalizeMode(?string $mode): ?string
    {
        $mode = trim((string) $mode);

        if ($mode === '') {
            return null;
        }

        return match ($mode) {
            self::MODE_PERCENTAGE, self::MODE_FIXED_DELTA, self::MODE_FIXED_PRICE => $mode,
            'direct', 'fixed' => self::MODE_FIXED_PRICE,
            default => null,
        };
    }

    /**
     * @return list<string>
     */
    public function allowedModesForProviderCost(bool $providerCostAvailable): array
    {
        if ($providerCostAvailable) {
            return ['', self::MODE_PERCENTAGE, self::MODE_FIXED_DELTA, self::MODE_FIXED_PRICE];
        }

        return [self::MODE_FIXED_PRICE];
    }

    private function applyOperatorAdjustment(string $pricingMode, float $price, ?float $providerUnitCost): ?float
    {
        return match ($pricingMode) {
            self::MODE_PERCENTAGE => $providerUnitCost !== null
                ? $providerUnitCost * (1.0 + ($price / 100.0))
                : null,
            self::MODE_FIXED_DELTA => $providerUnitCost !== null
                ? $providerUnitCost + $price
                : null,
            self::MODE_FIXED_PRICE => $price > 0.0 ? $price : null,
            default => null,
        };
    }
}
