<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CurrencyRateSide;
use App\Models\Currency;

/**
 * Builds a locale-currency hint line for the operator service preview price.
 */
final class OperatorPreviewLocalePriceService
{
    public function __construct(
        private readonly CurrencyConversionService $conversion,
        private readonly PriceFormatService $priceFormat,
    ) {
    }

    /**
     * @param  array<string, mixed>|null  $operatorPrice
     */
    public function build(?array $operatorPrice, int $operatorAccountId): ?string
    {
        return $this->buildForTarget(
            $operatorPrice,
            $operatorAccountId,
            locale_currency_code(),
            'account.service_offers.operator_preview_locale_price',
        );
    }

    /**
     * @param  array<string, mixed>|null  $operatorPrice
     */
    public function buildUsd(?array $operatorPrice, int $operatorAccountId): ?string
    {
        return $this->buildForTarget(
            $operatorPrice,
            $operatorAccountId,
            'USD',
            'account.service_offers.operator_index_usd_price',
        );
    }

    /**
     * @param  array<string, mixed>|null  $operatorPrice
     */
    private function buildForTarget(
        ?array $operatorPrice,
        int $operatorAccountId,
        string $targetCode,
        string $translationKey,
    ): ?string {
        if (! is_array($operatorPrice) || ! ($operatorPrice['has_amount'] ?? false)) {
            return null;
        }

        $amount = $operatorPrice['amount'] ?? null;
        if ($amount === null) {
            return null;
        }

        $sourceCode = strtoupper(trim((string) ($operatorPrice['currency_code'] ?? '')));
        $targetCode = strtoupper(trim($targetCode));

        if ($sourceCode === '' || $sourceCode === '—' || $sourceCode === $targetCode) {
            return null;
        }

        $sourceCurrency = Currency::resolveByIsoCode($sourceCode);
        $targetCurrency = Currency::resolveByIsoCode($targetCode);

        if ($sourceCurrency === null || $targetCurrency === null) {
            return null;
        }

        $converted = $this->conversion->convert(
            (float) $amount,
            (int) $sourceCurrency->id,
            (int) $targetCurrency->id,
            CurrencyRateSide::Buy,
            CurrencyRateSide::Sell,
            $operatorAccountId,
        );

        if ($converted === null) {
            return null;
        }

        $rateCurrency = $targetCode !== 'USD' ? $targetCurrency : $sourceCurrency;
        $rateSide = $targetCode !== 'USD' ? CurrencyRateSide::Sell : CurrencyRateSide::Buy;

        $rateUnits = $this->conversion->unitsPerUsdAt(
            (int) $rateCurrency->id,
            $rateSide,
            $operatorAccountId,
        );

        if ($rateUnits === null) {
            return null;
        }

        $rateRow = $this->conversion->effectiveRateRow(
            (int) $rateCurrency->id,
            $operatorAccountId,
        );

        $rateDate = $rateRow?->starting_at !== null
            ? locale_date($rateRow->starting_at)
            : locale_date(now());

        $convertedLabel = $targetCode.' '.$this->priceFormat->format(
            $converted,
            $operatorAccountId,
        );

        $rateSymbol = trim((string) ($rateCurrency->lmpCurrency?->symbol ?? ''));
        $rateAmount = $this->priceFormat->format($rateUnits, $operatorAccountId, 0);
        $rateLabel = ($rateSymbol !== '' ? $rateSymbol : '$').$rateAmount;

        return __($translationKey, [
            'amount' => $convertedLabel,
            'rate' => $rateLabel,
            'date' => $rateDate,
        ]);
    }
}
