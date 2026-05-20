<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Currency;
use App\Models\CurrencyRate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Account-area exchange rates: read system reference, edit tenant overrides (one set per calendar day).
 */
final class AccountExchangeRateService
{
    public function __construct(private readonly CurrencyConversionService $currencyConversion)
    {
    }

    /**
     * @return Collection<int, Currency>
     */
    public function projectCurrencies(): Collection
    {
        return Currency::query()->with('lmpCurrency')->orderBy('id')->get();
    }

    public function parseRateDay(?string $date): Carbon
    {
        if ($date !== null && trim($date) !== '') {
            try {
                return Carbon::parse($date)->timezone(config('app.timezone'))->startOfDay();
            } catch (\Throwable) {
                // fall through
            }
        }

        return Carbon::today();
    }

    /**
     * System rates effective on the given day (read-only reference).
     *
     * @return list<array{
     *     currency_id: int,
     *     code: string,
     *     label: string,
     *     buy: float|null,
     *     sell: float|null,
     *     source: string|null,
     *     is_usd: bool
     * }>
     */
    public function systemRowsForDay(Carbon $rateDay): array
    {
        return $this->mapCurrenciesToRows(null, $rateDay, forTenant: false);
    }

    /**
     * Tenant rates stored for the calendar day (any source).
     *
     * @return list<array{
     *     rate_id: int|null,
     *     currency_id: int,
     *     code: string,
     *     label: string,
     *     buy: float|null,
     *     sell: float|null,
     *     is_active: bool,
     *     source: string|null,
     *     is_usd: bool
     * }>
     */
    public function tenantRowsForDay(int $accountId, Carbon $rateDay): array
    {
        return $this->mapCurrenciesToRows($accountId, $rateDay, forTenant: true);
    }

    public function tenantHasRatesForDay(int $accountId, Carbon $rateDay): bool
    {
        return CurrencyRate::query()
            ->where('account_id', $accountId)
            ->whereDate('starting_at', $rateDay)
            ->exists();
    }

    /**
     * Distinct calendar days with tenant rates, newest first.
     *
     * @return list<array{date: string, label: string, active_count: int, total: int}>
     */
    public function tenantRateDaySummaries(int $accountId, int $limit = 30): array
    {
        $rows = CurrencyRate::query()
            ->where('account_id', $accountId)
            ->orderByDesc('starting_at')
            ->orderByDesc('id')
            ->get(['id', 'starting_at', 'is_active']);

        $grouped = $rows->groupBy(fn (CurrencyRate $r): string => $r->starting_at->timezone(config('app.timezone'))->toDateString());

        $summaries = [];
        foreach ($grouped->take($limit) as $date => $dayRows) {
            $summaries[] = [
                'date' => $date,
                'label' => Carbon::parse($date)->translatedFormat('d M Y'),
                'active_count' => $dayRows->where('is_active', true)->count(),
                'total' => $dayRows->count(),
            ];
        }

        return $summaries;
    }

    /**
     * Form rows for edit: from system snapshot or existing tenant rows.
     *
     * @return list<array{
     *     rate_id: int|null,
     *     currency_id: int,
     *     code: string,
     *     label: string,
     *     buy: string,
     *     sell: string,
     *     is_active: bool,
     *     is_usd: bool,
     *     buy_disabled: bool,
     *     sell_disabled: bool
     * }>
     */
    public function formRowsForEdit(int $accountId, Carbon $rateDay, bool $fromSystem): array
    {
        if ($fromSystem) {
            return $this->formRowsFromSnapshot($this->systemRowsForDay($rateDay));
        }

        $tenant = $this->tenantRowsForDay($accountId, $rateDay);
        if ($tenant === []) {
            return $this->formRowsFromSnapshot($this->systemRowsForDay($rateDay));
        }

        return $this->formRowsFromSnapshot($tenant);
    }

    /**
     * @param  list<array<string, mixed>>  $validatedRows
     */
    public function saveTenantRatesForDay(int $accountId, Carbon $rateDay, array $validatedRows): void
    {
        foreach ($validatedRows as $row) {
            $currencyId = (int) $row['currency_id'];
            $isUsd = Currency::isUsdProjectCurrency($currencyId);

            $buy = $isUsd ? 1.0 : (float) $row['units_per_usd_buy'];
            $sell = $isUsd ? 1.0 : (float) $row['units_per_usd_sell'];

            $existing = CurrencyRate::query()
                ->where('account_id', $accountId)
                ->where('currency_id', $currencyId)
                ->whereNull('source')
                ->whereDate('starting_at', $rateDay)
                ->first();

            $payload = [
                'units_per_usd_buy' => $buy,
                'units_per_usd_sell' => $sell,
                'is_active' => (bool) ($row['is_active'] ?? true),
            ];

            if ($existing !== null) {
                $existing->update($payload);

                continue;
            }

            CurrencyRate::query()->create([
                'account_id' => $accountId,
                'currency_id' => $currencyId,
                'source' => null,
                'starting_at' => $rateDay->copy(),
                ...$payload,
            ]);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function mapCurrenciesToRows(?int $accountId, Carbon $rateDay, bool $forTenant): array
    {
        $rows = [];

        foreach ($this->projectCurrencies() as $currency) {
            $code = $currency->currency_code;
            $isUsd = Currency::isUsdProjectCurrency((int) $currency->id);

            if ($forTenant) {
                $rate = CurrencyRate::query()
                    ->where('account_id', $accountId)
                    ->where('currency_id', $currency->id)
                    ->whereNull('source')
                    ->whereDate('starting_at', $rateDay)
                    ->orderByDesc('id')
                    ->first();

                $rows[] = [
                    'rate_id' => $rate?->id,
                    'currency_id' => (int) $currency->id,
                    'code' => $code,
                    'label' => $currency->display_name,
                    'buy' => $isUsd ? 1.0 : ($rate !== null ? (float) $rate->units_per_usd_buy : null),
                    'sell' => $isUsd ? 1.0 : ($rate !== null ? (float) $rate->units_per_usd_sell : null),
                    'is_active' => $rate?->is_active ?? true,
                    'source' => $rate?->source,
                    'is_usd' => $isUsd,
                ];

                continue;
            }

            $rate = $this->currencyConversion->effectiveRateRow((int) $currency->id, null, $rateDay);

            $rows[] = [
                'currency_id' => (int) $currency->id,
                'code' => $code,
                'label' => $currency->display_name,
                'buy' => $isUsd ? 1.0 : ($rate !== null ? $rate->unitsPerUsd(\App\Enums\CurrencyRateSide::Buy) : null),
                'sell' => $isUsd ? 1.0 : ($rate !== null ? $rate->unitsPerUsd(\App\Enums\CurrencyRateSide::Sell) : null),
                'source' => $rate?->source,
                'is_usd' => $isUsd,
            ];
        }

        return $rows;
    }

    /**
     * @param  list<array<string, mixed>>  $snapshot
     * @return list<array<string, mixed>>
     */
    private function formRowsFromSnapshot(array $snapshot): array
    {
        $formRows = [];

        foreach ($snapshot as $row) {
            $isUsd = (bool) ($row['is_usd'] ?? false);
            $buy = $row['buy'] ?? null;
            $sell = $row['sell'] ?? null;

            $formRows[] = [
                'rate_id' => $row['rate_id'] ?? null,
                'currency_id' => (int) $row['currency_id'],
                'code' => (string) $row['code'],
                'label' => (string) $row['label'],
                'buy' => $buy !== null ? $this->formatRateInput($buy) : '',
                'sell' => $sell !== null ? $this->formatRateInput($sell) : '',
                'is_active' => (bool) ($row['is_active'] ?? true),
                'is_usd' => $isUsd,
                'buy_disabled' => $isUsd,
                'sell_disabled' => $isUsd,
            ];
        }

        return $formRows;
    }

    private function formatRateInput(float $value): string
    {
        return rtrim(rtrim(number_format($value, 8, '.', ''), '0'), '.') ?: '0';
    }
}
