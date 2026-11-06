<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PriceAdjustmentType;
use App\Models\ProviderPassengerPolicyRule;
use App\Models\ServicePriceRule;
use App\Models\ServiceVariant;
use App\Models\ServiceVariantPaxBand;
use Illuminate\Support\Collection;

/**
 * Passenger-policy prices an operator sees on a proposed variant.
 *
 * Band amounts use the same commercial scale as a quote (list percentage and
 * assignment markup, never a fixed list price). Age adjustments apply only
 * when the variant has no party-size bands.
 */
final class OperatorPassengerPricingSummary
{
    public function __construct(
        private readonly OperatorVariantPriceResolver $tariffs,
        private readonly PriceFormatService $prices,
    ) {
    }

    /**
     * @return array{
     *     policy_name: string|null,
     *     note: string,
     *     headers: list<string>,
     *     rows: list<list<string>>,
     *     lines: list<string>
     * }|null
     */
    public function summarize(ServiceVariant $variant, int $providerAccountId, int $operatorAccountId): ?array
    {
        $variant->loadMissing([
            'paxBands.prices',
            'service.passengerPolicy.rules.category.translations.language.locale',
            'passengerPriceRules',
            'currency.lmpCurrency',
        ]);

        $rules = $this->policyRules($variant);
        $bands = $variant->paxBands;

        if ($bands->isEmpty() && $rules->isEmpty()) {
            return null;
        }

        $scale = $this->tariffs->paxBandCommercialScale($variant, $providerAccountId, $operatorAccountId);
        $currencyCode = $scale['failed']
            ? $this->variantCurrencyCode($variant)
            : (string) $scale['currency_code'];

        $notes = [];
        if ($bands->isNotEmpty()) {
            $built = $this->bandTable($bands, $rules, $scale, $currencyCode, $operatorAccountId);
            $notes[] = __('account.service_offers.operator_passenger_note_bands');
        } else {
            $built = $this->ageTable(
                $variant,
                $rules,
                $this->variantCurrencyCode($variant),
                $operatorAccountId,
            );
            $notes[] = __('account.service_offers.operator_passenger_note_ages');
        }

        if ($scale['failed'] && $bands->isNotEmpty()) {
            $notes[] = __('account.service_offers.operator_passenger_note_fx');
        }

        $policyName = trim((string) ($variant->service?->passengerPolicy?->name ?? ''));

        return [
            'policy_name' => $policyName !== '' ? $policyName : null,
            'note' => implode(' ', $notes),
            'headers' => $built['headers'],
            'rows' => $built['rows'],
            'lines' => $built['lines'],
        ];
    }

    /**
     * @return Collection<int, ProviderPassengerPolicyRule>
     */
    private function policyRules(ServiceVariant $variant): Collection
    {
        $rules = $variant->service?->passengerPolicy?->rules;

        if ($rules === null || $rules->isEmpty()) {
            return collect();
        }

        return $rules
            ->sortBy(fn (ProviderPassengerPolicyRule $rule): array => [(int) $rule->age_min, (int) $rule->id])
            ->values();
    }

    /**
     * @param  Collection<int, ServiceVariantPaxBand>  $bands
     * @param  Collection<int, ProviderPassengerPolicyRule>  $rules
     * @param  array{failed: bool, factor: float, offset: float, currency_code: string}  $scale
     * @return array{headers: list<string>, rows: list<list<string>>, lines: list<string>}
     */
    private function bandTable(
        Collection $bands,
        Collection $rules,
        array $scale,
        string $currencyCode,
        int $operatorAccountId,
    ): array {
        $headers = [
            __('account.service_offers.operator_passenger_people'),
            __('account.service_offers.operator_passenger_regular'),
        ];

        foreach ($rules as $rule) {
            $headers[] = $this->categoryHeading($rule);
        }

        $rows = [];
        $lines = [];

        foreach ($bands as $band) {
            $regularRaw = (float) $band->price;
            $regular = $this->money($regularRaw, $scale, $currencyCode, $operatorAccountId);
            $cells = [$this->paxRange($band), $regular];
            $extras = [];

            foreach ($rules as $rule) {
                $raw = $band->amountForPolicyRule((int) $rule->id);
                $formatted = $this->money($raw, $scale, $currencyCode, $operatorAccountId);
                $cells[] = $formatted;

                if (abs($raw - $regularRaw) >= 0.001) {
                    $extras[] = trim($this->categoryName($rule).' '.$formatted);
                }
            }

            $rows[] = $cells;
            $line = $this->paxRange($band).': '.$regular;
            if ($extras !== []) {
                $line .= ' · '.implode(' · ', $extras);
            }
            $lines[] = $line;
        }

        return [
            'headers' => $headers,
            'rows' => $rows,
            'lines' => $lines,
        ];
    }

    /**
     * @param  Collection<int, ProviderPassengerPolicyRule>  $rules
     * @return array{headers: list<string>, rows: list<list<string>>, lines: list<string>}
     */
    private function ageTable(
        ServiceVariant $variant,
        Collection $rules,
        string $currencyCode,
        int $operatorAccountId,
    ): array {
        $stored = $variant->passengerPriceRules
            ->filter(fn (ServicePriceRule $rule): bool => $rule->provider_price_list_item_id === null)
            ->keyBy(fn (ServicePriceRule $rule): int => (int) $rule->provider_passenger_policy_rule_id);

        $rows = [];
        $lines = [];

        foreach ($rules as $rule) {
            $name = $this->categoryName($rule);
            $ages = $this->ageRange($rule);
            $fare = $this->adjustmentLabel($stored->get((int) $rule->id), $currencyCode, $operatorAccountId);
            $rows[] = [$name, $ages, $fare];
            $lines[] = $name.' ('.$ages.'): '.$fare;
        }

        return [
            'headers' => [
                __('account.service_offers.operator_passenger_category'),
                __('account.service_offers.operator_passenger_ages'),
                __('account.service_offers.operator_passenger_fare'),
            ],
            'rows' => $rows,
            'lines' => $lines,
        ];
    }

    private function adjustmentLabel(?ServicePriceRule $rule, string $currencyCode, int $operatorAccountId): string
    {
        $regular = __('account.service_offers.operator_passenger_regular_fare');
        if ($rule === null) {
            return $regular;
        }

        $type = $rule->adjustment_type instanceof PriceAdjustmentType
            ? $rule->adjustment_type
            : PriceAdjustmentType::tryFrom((string) $rule->adjustment_type);

        if ($type === null) {
            return $regular;
        }

        $value = (float) $rule->adjustment_value;
        if ($type->isPercentage()) {
            return $type->label().' '.$this->prices->format($value, $operatorAccountId).'%';
        }

        return $type->label().' '.$this->prices->formatWithCurrency(
            $value,
            currencyCode: $currencyCode,
            accountId: $operatorAccountId,
        );
    }

    /**
     * @param  array{failed: bool, factor: float, offset: float}  $scale
     */
    private function money(float $raw, array $scale, string $currencyCode, int $operatorAccountId): string
    {
        $amount = $scale['failed']
            ? $raw
            : round(($raw * $scale['factor']) + $scale['offset'], 2);

        return $this->prices->formatWithCurrency($amount, currencyCode: $currencyCode, accountId: $operatorAccountId);
    }

    private function variantCurrencyCode(ServiceVariant $variant): string
    {
        $variant->loadMissing('currency.lmpCurrency');

        return $variant->currency?->currency_code ?? '';
    }

    private function categoryHeading(ProviderPassengerPolicyRule $rule): string
    {
        return trim($this->categoryName($rule).' ('.$this->ageRange($rule).')');
    }

    private function categoryName(ProviderPassengerPolicyRule $rule): string
    {
        $rule->loadMissing('category.translations.language.locale');

        return trim((string) ($rule->category?->name ?? ''));
    }

    private function paxRange(ServiceVariantPaxBand $band): string
    {
        if ($band->max_pax === null) {
            return __('account.service_offers.operator_passenger_pax_open', ['min' => $band->min_pax]);
        }

        if ((int) $band->min_pax === (int) $band->max_pax) {
            return __('account.service_offers.operator_passenger_pax_exact', ['count' => $band->min_pax]);
        }

        return __('account.service_offers.operator_passenger_pax_range', [
            'min' => $band->min_pax,
            'max' => $band->max_pax,
        ]);
    }

    private function ageRange(ProviderPassengerPolicyRule $rule): string
    {
        if ($rule->age_max === null) {
            return __('account.service_offers.operator_passenger_age_open', ['min' => $rule->age_min]);
        }

        return __('account.service_offers.operator_passenger_age_range', [
            'min' => $rule->age_min,
            'max' => $rule->age_max,
        ]);
    }
}
