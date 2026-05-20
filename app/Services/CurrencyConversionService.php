<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CurrencyRateSide;
use App\Models\Currency;
use App\Models\CurrencyRate;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * Converts amounts between project currencies (cat_currencies) using currency_rates.
 *
 * Rates are stored as units_per_usd_buy / units_per_usd_sell (1 USD = N units in that currency).
 * Each conversion must specify which side to use per currency leg.
 *
 * Resolution per currency and date:
 * 1. Latest active row for the tenant account (account_id), if provided.
 * 2. Else latest active system row (account_id null).
 *
 * source is reserved for future multi-source rates; pass null to use the default (source IS NULL) rows.
 */
final class CurrencyConversionService
{
    /**
     * units_per_usd for the given currency on the pricing date (latest active rate on or before that day).
     */
    public function unitsPerUsdAt(
        int $currencyId,
        CurrencyRateSide $side,
        ?int $accountId = null,
        ?CarbonInterface $asOfDate = null,
        ?string $source = null,
    ): ?float {
        if (Currency::isUsdProjectCurrency($currencyId)) {
            return 1.0;
        }

        $row = $this->effectiveRateRow($currencyId, $accountId, $asOfDate, $source);
        if ($row === null) {
            return null;
        }

        $units = $row->unitsPerUsd($side);

        return $units > 0.0 ? $units : null;
    }

    /**
     * Whether a non-USD conversion rate exists for the date (USD always returns true).
     */
    public function hasRateAt(
        int $currencyId,
        CurrencyRateSide $side,
        ?int $accountId = null,
        ?CarbonInterface $asOfDate = null,
        ?string $source = null,
    ): bool {
        return $this->unitsPerUsdAt($currencyId, $side, $accountId, $asOfDate, $source) !== null;
    }

    /**
     * The currency_rates row in effect for the date, or null when none exists (USD has no row requirement).
     */
    public function effectiveRateRow(
        int $currencyId,
        ?int $accountId = null,
        ?CarbonInterface $asOfDate = null,
        ?string $source = null,
    ): ?CurrencyRate {
        if (Currency::isUsdProjectCurrency($currencyId)) {
            return null;
        }

        $cutoff = $this->cutoffForDate($asOfDate);

        if ($accountId !== null) {
            $tenantRow = $this->rateQuery($currencyId, $accountId, $source, $cutoff)->first();
            if ($tenantRow !== null) {
                return $tenantRow;
            }
        }

        return $this->rateQuery($currencyId, null, $source, $cutoff)->first();
    }

    /**
     * Convert an amount from one project currency to another at the effective rates for the date.
     *
     * Typical cost valuation (provider price → operator list currency): buy on source, sell on destination.
     */
    public function convert(
        float $amount,
        int $fromCurrencyId,
        int $toCurrencyId,
        CurrencyRateSide $fromSide,
        CurrencyRateSide $toSide,
        ?int $accountId = null,
        ?CarbonInterface $asOfDate = null,
        ?string $source = null,
    ): ?float {
        if ($fromCurrencyId === $toCurrencyId) {
            return $amount;
        }

        $fromUnits = $this->unitsPerUsdAt($fromCurrencyId, $fromSide, $accountId, $asOfDate, $source);
        $toUnits = $this->unitsPerUsdAt($toCurrencyId, $toSide, $accountId, $asOfDate, $source);

        if ($fromUnits === null || $toUnits === null) {
            return null;
        }

        $amountInUsd = $amount / $fromUnits;

        return $amountInUsd * $toUnits;
    }

    /**
     * @return Builder<CurrencyRate>
     */
    private function rateQuery(int $currencyId, ?int $accountId, ?string $source, Carbon $cutoff): Builder
    {
        $query = CurrencyRate::query()
            ->active()
            ->where('currency_id', $currencyId)
            ->where('starting_at', '<=', $cutoff);

        if ($accountId === null) {
            $query->whereNull('account_id');
        } else {
            $query->where('account_id', $accountId);
        }

        if ($source === null || $source === '') {
            $query->where(function (Builder $q): void {
                $q->whereNull('source')
                    ->orWhere('source', DolarApiCurrencyRateImporter::SOURCE);
            });
        } else {
            $query->where('source', $source);
        }

        $query->orderByDesc('starting_at');

        if ($source === null || $source === '') {
            $query->orderByRaw(
                'CASE WHEN source = ? THEN 0 WHEN source IS NULL THEN 1 ELSE 2 END',
                [DolarApiCurrencyRateImporter::SOURCE],
            );
        }

        return $query->orderByDesc('id');
    }

    private function cutoffForDate(?CarbonInterface $asOfDate): Carbon
    {
        return Carbon::parse($asOfDate ?? Carbon::today())->endOfDay();
    }
}
