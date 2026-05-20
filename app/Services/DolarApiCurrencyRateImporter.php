<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Currency;
use App\Models\CurrencyRate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Imports system currency_rates rows from dolarapi.com cotizaciones (ARS per 1 unit of foreign currency).
 *
 * API sample: { "moneda": "USD", "casa": "oficial", "compra": 1370, "venta": 1420, ... }
 * Stored as units_per_usd_* (1 USD = N units in each cat_currencies row).
 */
final class DolarApiCurrencyRateImporter
{
    public const SOURCE = 'dolarapi';

    /**
     * @param  list<array<string, mixed>>  $quotes
     * @return array{saved: int, skipped: int, messages: list<string>}
     */
    public function import(
        array $quotes,
        Collection $currencies,
        string $casa = 'oficial',
        ?Carbon $startingAt = null,
    ): array {
        $indexed = $this->indexOfficialQuotes($quotes, $casa);
        if ($indexed === null) {
            return [
                'saved' => 0,
                'skipped' => 0,
                'messages' => ['No official USD quote found in API response.'],
            ];
        }

        $usd = $indexed['USD'];
        $rateDay = ($startingAt ?? $this->resolveRateDay($usd['fechaActualizacion'] ?? null))->copy()->startOfDay();

        $saved = 0;
        $skipped = 0;
        $messages = [];

        foreach ($currencies as $currency) {
            $code = strtoupper(trim((string) ($currency->lmpCurrency?->code ?? '')));
            if ($code === '') {
                $messages[] = "Currency id {$currency->id} has no lmp_currency code; skipped.";
                $skipped++;

                continue;
            }

            $legs = $this->computeUnitsPerUsd($code, $indexed, $usd);
            if ($legs === null) {
                $messages[] = "No quote to derive units_per_usd for {$code} (currency id {$currency->id}); skipped.";
                $skipped++;

                continue;
            }

            $this->upsertSystemRateForDay(
                currencyId: (int) $currency->id,
                rateDay: $rateDay,
                buy: $legs['buy'],
                sell: $legs['sell'],
            );

            $saved++;
        }

        return compact('saved', 'skipped', 'messages');
    }

    /**
     * @param  list<array<string, mixed>>  $quotes
     * @return array<string, array{compra: float, venta: float, fechaActualizacion?: string}>|null
     */
    private function indexOfficialQuotes(array $quotes, string $casa): ?array
    {
        $casa = strtolower(trim($casa));
        $indexed = [];

        foreach ($quotes as $row) {
            if (! is_array($row)) {
                continue;
            }
            if (strtolower(trim((string) ($row['casa'] ?? ''))) !== $casa) {
                continue;
            }
            $moneda = strtoupper(trim((string) ($row['moneda'] ?? '')));
            if ($moneda === '') {
                continue;
            }
            $compra = (float) ($row['compra'] ?? 0);
            $venta = (float) ($row['venta'] ?? 0);
            if ($compra <= 0.0 || $venta <= 0.0) {
                continue;
            }
            $indexed[$moneda] = [
                'compra' => $compra,
                'venta' => $venta,
                'fechaActualizacion' => isset($row['fechaActualizacion']) ? (string) $row['fechaActualizacion'] : null,
            ];
        }

        return isset($indexed['USD']) ? $indexed : null;
    }

    /**
     * @param  array<string, array{compra: float, venta: float, fechaActualizacion?: string|null}>  $indexed
     * @param  array{compra: float, venta: float, fechaActualizacion?: string|null}  $usd
     * @return array{buy: float, sell: float}|null
     */
    private function computeUnitsPerUsd(string $code, array $indexed, array $usd): ?array
    {
        if ($code === 'USD') {
            return ['buy' => 1.0, 'sell' => 1.0];
        }

        if ($code === 'ARS') {
            return ['buy' => $usd['compra'], 'sell' => $usd['venta']];
        }

        $foreign = $indexed[$code] ?? null;
        if ($foreign === null) {
            return null;
        }

        return [
            'buy' => $usd['compra'] / $foreign['compra'],
            'sell' => $usd['venta'] / $foreign['venta'],
        ];
    }

    /**
     * Calendar day for the rate (app timezone). At most one dolarapi row per currency per day.
     */
    private function resolveRateDay(?string $fechaActualizacion): Carbon
    {
        if ($fechaActualizacion !== null && trim($fechaActualizacion) !== '') {
            try {
                return Carbon::parse($fechaActualizacion)
                    ->timezone(config('app.timezone'))
                    ->startOfDay();
            } catch (\Throwable) {
                // fall through
            }
        }

        return Carbon::today();
    }

    private function upsertSystemRateForDay(int $currencyId, Carbon $rateDay, float $buy, float $sell): CurrencyRate
    {
        $existing = CurrencyRate::query()
            ->whereNull('account_id')
            ->where('currency_id', $currencyId)
            ->where('source', self::SOURCE)
            ->whereDate('starting_at', $rateDay)
            ->first();

        $payload = [
            'units_per_usd_buy' => $buy,
            'units_per_usd_sell' => $sell,
            'is_active' => true,
        ];

        if ($existing !== null) {
            $existing->update($payload);

            return $existing;
        }

        return CurrencyRate::query()->create([
            'account_id' => null,
            'currency_id' => $currencyId,
            'source' => self::SOURCE,
            'starting_at' => $rateDay->copy(),
            ...$payload,
        ]);
    }
}
