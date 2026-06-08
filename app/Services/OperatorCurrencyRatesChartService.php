<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CurrencyRateSide;
use App\Models\Currency;
use Illuminate\Support\Carbon;

/**
 * System reference FX data for the operator dashboard line chart (last 7 calendar days).
 */
final class OperatorCurrencyRatesChartService
{
    private const HISTORY_DAYS = 7;

    public function __construct(private readonly CurrencyConversionService $conversion)
    {
    }

    /**
     * @return array{
     *     currencies: list<array{
     *         id: int,
     *         code: string,
     *         label: string,
     *         current: array{
     *             buy: float|null,
     *             sell: float|null,
     *             updated_at_human: string|null,
     *         },
     *         series: array{
     *             labels: list<string>,
     *             buy: list<float|null>,
     *             sell: list<float|null>,
     *         },
     *     }>,
     *     default_currency_id: int|null,
     *     strings: array{buy: string, sell: string, empty: string, per_usd: string, heading: string, updated: string},
     * }
     */
    public function build(): array
    {
        $days = $this->lastCalendarDays();
        $currencies = [];

        foreach (Currency::query()->with('lmpCurrency')->orderBy('id')->get() as $currency) {
            $currencyId = (int) $currency->id;

            if (Currency::isUsdProjectCurrency($currencyId)) {
                continue;
            }

            $labels = [];
            $buySeries = [];
            $sellSeries = [];
            $hasAny = false;

            foreach ($days as $day) {
                $row = $this->conversion->effectiveRateRow($currencyId, null, $day);
                $labels[] = locale_date($day);

                if ($row === null) {
                    $buySeries[] = null;
                    $sellSeries[] = null;

                    continue;
                }

                $buySeries[] = $row->unitsPerUsd(CurrencyRateSide::Buy);
                $sellSeries[] = $row->unitsPerUsd(CurrencyRateSide::Sell);
                $hasAny = true;
            }

            if (! $hasAny) {
                continue;
            }

            $currentRow = $this->conversion->effectiveRateRow($currencyId, null, Carbon::today());
            $updatedAt = $currentRow?->updated_at;

            $currencies[] = [
                'id' => $currencyId,
                'code' => $currency->currency_code,
                'label' => $currency->display_name,
                'current' => [
                    'buy' => $currentRow !== null ? $currentRow->unitsPerUsd(CurrencyRateSide::Buy) : null,
                    'sell' => $currentRow !== null ? $currentRow->unitsPerUsd(CurrencyRateSide::Sell) : null,
                    'updated_at_human' => $updatedAt !== null
                        ? Carbon::parse($updatedAt)->locale(locale_for_carbon())->diffForHumans()
                        : null,
                ],
                'series' => [
                    'labels' => $labels,
                    'buy' => $buySeries,
                    'sell' => $sellSeries,
                ],
            ];
        }

        return [
            'currencies' => $currencies,
            'default_currency_id' => $currencies[0]['id'] ?? null,
            'strings' => [
                'buy' => (string) __('exchange_rates.columns.buy'),
                'sell' => (string) __('exchange_rates.columns.sell'),
                'empty' => (string) __('operator_dashboard.currency_chart_empty'),
                'per_usd' => (string) __('operator_dashboard.currency_chart_per_usd'),
                'heading' => (string) __('operator_dashboard.currency_chart_heading'),
                'updated' => (string) __('operator_dashboard.currency_chart_updated'),
            ],
        ];
    }

    /**
     * @return list<Carbon>
     */
    private function lastCalendarDays(): array
    {
        $timezone = config('app.timezone');
        $today = Carbon::today($timezone);
        $days = [];

        for ($offset = self::HISTORY_DAYS - 1; $offset >= 0; $offset--) {
            $days[] = $today->copy()->subDays($offset);
        }

        return $days;
    }
}
