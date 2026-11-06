<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CurrencyRateSide;
use App\Models\OperatorPackageItem;
use App\Models\ServiceVariant;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Validation\ValidationException;

/**
 * Agency price of one package line. Per-person lines sum one fare per age.
 * The provider category adjustment is applied before the operator's own markup,
 * except when the operator sells a fixed price: then the same ratio is applied
 * to that selling price so the provider discount still reaches the agency.
 */
final class AgencyPassengerLinePricer
{
    public function __construct(
        private readonly OperatorPriceListItemPricingService $itemPricingService,
        private readonly PassengerCategoryFareResolver $fares,
        private readonly CurrencyConversionService $currencyConversion,
    ) {
    }

    /**
     * @param  list<int>  $ages
     * @return array{
     *     has_amount: bool,
     *     total: float|null,
     *     regular_unit: float|null,
     *     fares: list<array<string, mixed>>
     * }
     */
    public function price(
        OperatorPackageItem $packageItem,
        int $operatorAccountId,
        int $listCurrencyId,
        ?string $pricingMode,
        float $operatorPrice,
        CarbonInterface $pricingDate,
        array $ages,
    ): array {
        $empty = [
            'has_amount' => false,
            'total' => null,
            'regular_unit' => null,
            'fares' => [],
        ];

        $pricingMode = $this->itemPricingService->normalizeMode($pricingMode);
        $variant = $packageItem->serviceVariant;
        $providerId = (int) ($packageItem->serviceOffer?->provider_id ?? 0);
        $packageQuantity = max(1, (int) $packageItem->quantity);

        if (! $variant instanceof ServiceVariant || $providerId < 1) {
            return $empty;
        }

        $regular = $this->itemPricingService->calculate(
            $packageItem,
            $operatorAccountId,
            $listCurrencyId,
            $pricingMode,
            $operatorPrice,
            $pricingDate,
        );

        if (! $regular['final_price_has'] || $regular['final_price'] === null) {
            return $empty;
        }

        $regularUnit = (float) $regular['final_price'];
        $variant->loadMissing('paxBands');
        $usesBands = $variant->usesPaxBands();
        $partySize = count($ages);

        if ($variant->pricing_type !== 'per_person' && ! $usesBands) {
            return [
                'has_amount' => true,
                'total' => round($regularUnit * $packageQuantity, 2),
                'regular_unit' => $regularUnit,
                'fares' => [],
            ];
        }

        if ($usesBands && $partySize < 1) {
            return $empty;
        }

        $sum = 0.0;
        $storedFares = [];

        foreach (array_values($ages) as $index => $age) {
            $priced = $this->fares->priceAge(
                $variant,
                $providerId,
                $operatorAccountId,
                (int) $age,
                $partySize,
                $pricingDate,
            );

            if ($priced['status'] === 'pax_unmatched') {
                throw ValidationException::withMessages([
                    'ages' => (string) __('account.reservations.validation.pax_without_price', [
                        'count' => $partySize,
                        'service' => (string) ($priced['fare']['service_name'] ?? $variant->sku),
                    ]),
                ]);
            }

            if ($priced['status'] === 'unmatched') {
                throw ValidationException::withMessages([
                    'ages.'.$index => (string) __('account.reservations.validation.age_without_category', [
                        'age' => (int) $age,
                        'service' => (string) ($priced['fare']['service_name'] ?? $variant->sku),
                    ]),
                ]);
            }

            if ($priced['status'] !== 'priced' || $priced['amount'] === null || $priced['regular_amount'] === null) {
                return $empty;
            }

            $providerAmount = $this->convertToListCurrency(
                (float) $priced['amount'],
                (int) $priced['currency_id'],
                $listCurrencyId,
                $operatorAccountId,
                $pricingDate,
            );
            $regularProvider = $this->convertToListCurrency(
                (float) $priced['regular_amount'],
                (int) $priced['currency_id'],
                $listCurrencyId,
                $operatorAccountId,
                $pricingDate,
            );

            if ($providerAmount === null || $regularProvider === null) {
                return $empty;
            }

            if ($providerAmount < 0) {
                throw ValidationException::withMessages([
                    'ages.'.$index => (string) __('account.reservations.validation.fare_negative', [
                        'age' => (int) $age,
                        'service' => trim((string) ($variant->service?->name ?? $variant->sku)),
                    ]),
                ]);
            }

            $agencyUnit = $this->agencyUnit(
                $pricingMode,
                $operatorPrice,
                $regularUnit,
                $regularProvider,
                $providerAmount,
                $usesBands,
                isset($priced['fare']['provider_base_amount']) ? (float) $priced['fare']['provider_base_amount'] : null,
                (float) $priced['amount'],
            );
            if ($agencyUnit === null || $agencyUnit < 0) {
                if ($agencyUnit !== null && $agencyUnit < 0) {
                    throw ValidationException::withMessages([
                        'ages.'.$index => (string) __('account.reservations.validation.fare_negative', [
                            'age' => (int) $age,
                            'service' => trim((string) ($variant->service?->name ?? $variant->sku)),
                        ]),
                    ]);
                }

                return $empty;
            }

            $sum += $agencyUnit;
            $fare = $priced['fare'];
            $fare['agency_amount'] = round($agencyUnit, 2);
            $storedFares[] = $fare;
        }

        return [
            'has_amount' => true,
            'total' => round($sum * $packageQuantity, 2),
            'regular_unit' => $regularUnit,
            'fares' => $storedFares,
        ];
    }

    private function agencyUnit(
        ?string $pricingMode,
        float $operatorPrice,
        float $regularAgencyUnit,
        float $regularProvider,
        float $categoryProvider,
        bool $usesPaxBands = false,
        ?float $providerBaseAmount = null,
        ?float $providerCellAmount = null,
    ): ?float {
        if ($pricingMode === OperatorPriceListItemPricingService::MODE_FIXED_PRICE) {
            if ($usesPaxBands && $providerBaseAmount !== null && $providerBaseAmount > 0.0 && $providerCellAmount !== null) {
                return round($regularAgencyUnit * ($providerCellAmount / $providerBaseAmount), 2);
            }

            if ($regularProvider <= 0.0) {
                return $regularAgencyUnit;
            }

            return round($regularAgencyUnit * ($categoryProvider / $regularProvider), 2);
        }

        if ($pricingMode === OperatorPriceListItemPricingService::MODE_PERCENTAGE) {
            return round($categoryProvider * (1.0 + ($operatorPrice / 100.0)), 2);
        }

        if ($pricingMode === OperatorPriceListItemPricingService::MODE_FIXED_DELTA) {
            return round($categoryProvider + $operatorPrice, 2);
        }

        return round($categoryProvider, 2);
    }

    private function convertToListCurrency(
        float $amount,
        int $sourceCurrencyId,
        int $listCurrencyId,
        int $operatorAccountId,
        CarbonInterface $pricingDate,
    ): ?float {
        if ($sourceCurrencyId === $listCurrencyId) {
            return $amount;
        }

        return $this->currencyConversion->convert(
            $amount,
            $sourceCurrencyId,
            $listCurrencyId,
            CurrencyRateSide::Buy,
            CurrencyRateSide::Sell,
            $operatorAccountId,
            Carbon::parse($pricingDate)->startOfDay(),
        );
    }
}
