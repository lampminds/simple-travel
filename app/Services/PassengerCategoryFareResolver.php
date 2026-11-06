<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CurrencyRateSide;
use App\Enums\PriceAdjustmentType;
use App\Models\PriceListAssignment;
use App\Models\ProviderPassengerPolicyRule;
use App\Models\ServicePriceRule;
use App\Models\ServiceVariant;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * Applies a service passenger policy to one age, on top of the provider tariff.
 * The category adjustment is calculated before the provider-list assignment markup,
 * so that markup still applies to the differentiated amount.
 *
 * When the variant has pax bands, the party size selects the per-person amount and
 * the age cell replaces it. Variant and list age adjustments are not applied on top.
 */
final class PassengerCategoryFareResolver
{
    public function __construct(
        private readonly OperatorVariantPriceResolver $tariffs,
        private readonly CurrencyConversionService $currencyConversion,
    ) {
    }

    /**
     * @return array{
     *     status: string,
     *     regular_amount: float|null,
     *     amount: float|null,
     *     currency_id: int|null,
     *     currency_code: string,
     *     fare: array<string, mixed>
     * }
     */
    public function priceAge(
        ServiceVariant $variant,
        int $providerAccountId,
        int $operatorAccountId,
        int $age,
        int $partySize,
        ?CarbonInterface $pricingDate = null,
    ): array {
        $variant->loadMissing([
            'service.passengerPolicy.rules.category.translations.language.locale',
            'passengerPriceRules',
            'paxBands.prices',
        ]);

        if ($variant->usesPaxBands()) {
            return $this->priceAgeOnPaxBand(
                $variant,
                $providerAccountId,
                $operatorAccountId,
                $age,
                $partySize,
                $pricingDate,
            );
        }

        $tariff = $this->tariffs->resolveTariffBeforeAssignment(
            $variant,
            $providerAccountId,
            $operatorAccountId,
            $pricingDate,
        );

        $currencyCode = (string) ($tariff['currency_code'] ?? '—');
        $currencyId = isset($tariff['currency_id']) ? (int) $tariff['currency_id'] : null;

        if ($tariff['amount'] === null || $currencyId === null || $currencyId < 1) {
            return $this->result('unavailable', null, null, $currencyId, $currencyCode, [
                'age' => $age,
            ]);
        }

        $regularTariff = (float) $tariff['amount'];
        $assignment = $tariff['assignment'] instanceof PriceListAssignment ? $tariff['assignment'] : null;
        $band = $this->matchingBand($variant, $age);

        if ($band === false) {
            return $this->result('unmatched', null, null, $currencyId, $currencyCode, [
                'age' => $age,
                'service_name' => trim((string) ($variant->service?->name ?? $variant->sku)),
            ]);
        }

        $rule = $band instanceof ProviderPassengerPolicyRule
            ? $this->priceRule($variant, $band, $tariff['provider_price_list_item_id'])
            : null;

        $adjustedTariff = $regularTariff;
        $adjustmentType = null;
        $adjustmentValue = null;

        if ($rule instanceof ServicePriceRule) {
            $type = $rule->adjustment_type instanceof PriceAdjustmentType
                ? $rule->adjustment_type
                : PriceAdjustmentType::tryFrom((string) $rule->adjustment_type);

            if ($type === null) {
                return $this->result('unavailable', null, null, $currencyId, $currencyCode, ['age' => $age]);
            }

            $value = $this->valueInTariffCurrency(
                $rule,
                $type,
                $currencyId,
                (int) $tariff['variant_currency_id'],
                $operatorAccountId,
                $pricingDate,
            );

            if ($value === null) {
                return $this->result('unavailable', null, null, $currencyId, $currencyCode, ['age' => $age]);
            }

            $adjustedTariff = $type->apply($regularTariff, $value);
            $adjustmentType = $type->value;
            $adjustmentValue = $value;
        }

        $regularAmount = $assignment instanceof PriceListAssignment
            ? $this->tariffs->applyAssignmentAdjustment($regularTariff, $assignment)
            : $regularTariff;
        $amount = $assignment instanceof PriceListAssignment
            ? $this->tariffs->applyAssignmentAdjustment($adjustedTariff, $assignment)
            : $adjustedTariff;

        $fare = [
            'age' => $age,
            'category_code' => $band instanceof ProviderPassengerPolicyRule ? (string) ($band->category?->code ?? '') : null,
            'category_name' => $band instanceof ProviderPassengerPolicyRule ? (string) ($band->category?->name ?? '') : null,
            'age_min' => $band instanceof ProviderPassengerPolicyRule ? (int) $band->age_min : null,
            'age_max' => $band instanceof ProviderPassengerPolicyRule ? $band->age_max : null,
            'adjustment_type' => $adjustmentType,
            'adjustment_value' => $adjustmentValue,
            'regular_provider_amount' => round($regularAmount, 2),
            'provider_amount' => round($amount, 2),
            'provider_currency_code' => $currencyCode,
        ];

        return $this->result('priced', $regularAmount, $amount, $currencyId, $currencyCode, $fare);
    }

    /**
     * @return array{
     *     status: string,
     *     regular_amount: float|null,
     *     amount: float|null,
     *     currency_id: int|null,
     *     currency_code: string,
     *     fare: array<string, mixed>
     * }
     */
    private function priceAgeOnPaxBand(
        ServiceVariant $variant,
        int $providerAccountId,
        int $operatorAccountId,
        int $age,
        int $partySize,
        ?CarbonInterface $pricingDate,
    ): array {
        $paxBand = $variant->paxBandFor($partySize);
        $serviceName = trim((string) ($variant->service?->name ?? $variant->sku));

        if ($paxBand === null) {
            return $this->result('pax_unmatched', null, null, null, '—', [
                'age' => $age,
                'party_size' => $partySize,
                'service_name' => $serviceName,
            ]);
        }

        $ageBand = $this->matchingBand($variant, $age);
        if ($ageBand === false) {
            return $this->result('unmatched', null, null, null, '—', [
                'age' => $age,
                'party_size' => $partySize,
                'service_name' => $serviceName,
            ]);
        }

        $ruleId = $ageBand instanceof ProviderPassengerPolicyRule ? (int) $ageBand->id : null;
        $cell = $this->tariffs->adjustPaxBandAmount(
            $variant,
            $paxBand->amountForPolicyRule($ruleId),
            $providerAccountId,
            $operatorAccountId,
            $pricingDate,
        );
        $regular = $this->tariffs->adjustPaxBandAmount(
            $variant,
            (float) $paxBand->price,
            $providerAccountId,
            $operatorAccountId,
            $pricingDate,
        );

        $currencyCode = (string) ($cell['currency_code'] ?? '—');
        $currencyId = isset($cell['currency_id']) ? (int) $cell['currency_id'] : null;

        if ($cell['amount'] === null || $regular['amount'] === null || $currencyId === null || $currencyId < 1) {
            return $this->result('unavailable', null, null, $currencyId, $currencyCode, [
                'age' => $age,
                'party_size' => $partySize,
            ]);
        }

        $baseAmount = $variant->base_price !== null
            ? $this->tariffs->adjustPaxBandAmount(
                $variant,
                (float) $variant->base_price,
                $providerAccountId,
                $operatorAccountId,
                $pricingDate,
            )['amount']
            : null;

        $fare = [
            'age' => $age,
            'party_size' => $partySize,
            'pax_min' => (int) $paxBand->min_pax,
            'pax_max' => $paxBand->max_pax !== null ? (int) $paxBand->max_pax : null,
            'category_code' => $ageBand instanceof ProviderPassengerPolicyRule ? (string) ($ageBand->category?->code ?? '') : null,
            'category_name' => $ageBand instanceof ProviderPassengerPolicyRule ? (string) ($ageBand->category?->name ?? '') : null,
            'age_min' => $ageBand instanceof ProviderPassengerPolicyRule ? (int) $ageBand->age_min : null,
            'age_max' => $ageBand instanceof ProviderPassengerPolicyRule ? $ageBand->age_max : null,
            'adjustment_type' => null,
            'adjustment_value' => null,
            'regular_provider_amount' => round((float) $regular['amount'], 2),
            'provider_amount' => round((float) $cell['amount'], 2),
            'provider_base_amount' => $baseAmount !== null ? round((float) $baseAmount, 2) : null,
            'provider_currency_code' => $currencyCode,
        ];

        return $this->result('priced', (float) $regular['amount'], (float) $cell['amount'], $currencyId, $currencyCode, $fare);
    }

    /**
     * @return ProviderPassengerPolicyRule|false|null null when the service has no policy, false when the age matches no band
     */
    private function matchingBand(ServiceVariant $variant, int $age): ProviderPassengerPolicyRule|false|null
    {
        $policy = $variant->service?->passengerPolicy;
        if ($policy === null) {
            return null;
        }

        foreach ($policy->rules as $rule) {
            $min = (int) $rule->age_min;
            $max = $rule->age_max !== null ? (int) $rule->age_max : null;
            if ($age >= $min && ($max === null || $age <= $max)) {
                return $rule;
            }
        }

        return false;
    }

    private function priceRule(
        ServiceVariant $variant,
        ProviderPassengerPolicyRule $band,
        ?int $priceListItemId,
    ): ?ServicePriceRule {
        if ($priceListItemId !== null) {
            return ServicePriceRule::query()
                ->where('provider_price_list_item_id', $priceListItemId)
                ->where('provider_passenger_policy_rule_id', $band->id)
                ->first();
        }

        return $variant->passengerPriceRules
            ->first(fn (ServicePriceRule $rule): bool => (int) $rule->provider_passenger_policy_rule_id === (int) $band->id);
    }

    private function valueInTariffCurrency(
        ServicePriceRule $rule,
        PriceAdjustmentType $type,
        int $tariffCurrencyId,
        int $variantCurrencyId,
        int $operatorAccountId,
        ?CarbonInterface $pricingDate,
    ): ?float {
        $value = (float) $rule->adjustment_value;
        if ($type->isPercentage()) {
            return $value;
        }

        $sourceCurrencyId = $rule->provider_price_list_item_id !== null
            ? $tariffCurrencyId
            : $variantCurrencyId;

        if ($sourceCurrencyId === $tariffCurrencyId || $sourceCurrencyId < 1) {
            return $value;
        }

        return $this->currencyConversion->convert(
            $value,
            $sourceCurrencyId,
            $tariffCurrencyId,
            CurrencyRateSide::Buy,
            CurrencyRateSide::Sell,
            $operatorAccountId,
            Carbon::parse($pricingDate ?? Carbon::today())->startOfDay(),
        );
    }

    /**
     * @param  array<string, mixed>  $fare
     * @return array{
     *     status: string,
     *     regular_amount: float|null,
     *     amount: float|null,
     *     currency_id: int|null,
     *     currency_code: string,
     *     fare: array<string, mixed>
     * }
     */
    private function result(
        string $status,
        ?float $regularAmount,
        ?float $amount,
        ?int $currencyId,
        string $currencyCode,
        array $fare,
    ): array {
        return [
            'status' => $status,
            'regular_amount' => $regularAmount,
            'amount' => $amount,
            'currency_id' => $currencyId,
            'currency_code' => $currencyCode,
            'fare' => $fare,
        ];
    }
}
