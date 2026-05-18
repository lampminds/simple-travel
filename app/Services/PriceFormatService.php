<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Controllers\ParameterReaderController;
use App\Models\Currency;

/**
 * Formats and parses monetary amounts using tenant parameters from cat_parameters:
 *
 * - {@see CODE_PRICE_DECIMALS} (`price_decimals`): fractional digits (0–3, default 2).
 * - {@see CODE_PRICE_THOUSANDS_SEPARATOR} (`price_thousands_separator`): thousands separator (`,` or `.`);
 *   the decimal separator is the other character.
 */
final class PriceFormatService
{
    public const CODE_PRICE_DECIMALS = 'price_decimals';

    public const CODE_PRICE_THOUSANDS_SEPARATOR = 'price_thousands_separator';

    private const DEFAULT_DECIMALS = 2;

    private const DEFAULT_THOUSANDS_SEPARATOR = ',';

    public function __construct(private readonly ParameterReaderController $parameterReader)
    {
    }

    /**
     * Fractional digits for price display (`price_decimals`).
     */
    public function priceDecimals(?int $accountId = null): int
    {
        $decimals = $this->parameterReader->getInt(
            self::CODE_PRICE_DECIMALS,
            $accountId,
            self::DEFAULT_DECIMALS,
        );

        return max(0, min(3, $decimals));
    }

    /**
     * Thousands separator for price display (`price_thousands_separator`).
     */
    public function priceThousandsSeparator(?int $accountId = null): string
    {
        $thousands = trim((string) $this->parameterReader->getRawValue(
            self::CODE_PRICE_THOUSANDS_SEPARATOR,
            $accountId,
        ));

        if (! in_array($thousands, [',', '.'], true)) {
            return self::DEFAULT_THOUSANDS_SEPARATOR;
        }

        return $thousands;
    }

    /**
     * Decimal separator derived from {@see priceThousandsSeparator()} (`.` when thousands is `,`, and vice versa).
     */
    public function priceDecimalSeparator(?int $accountId = null): string
    {
        return $this->priceThousandsSeparator($accountId) === ',' ? '.' : ',';
    }

    /**
     * @return array{decimals:int, thousands_separator:string, decimal_separator:string}
     */
    public function resolveSettings(?int $accountId = null): array
    {
        return [
            'decimals' => $this->priceDecimals($accountId),
            'thousands_separator' => $this->priceThousandsSeparator($accountId),
            'decimal_separator' => $this->priceDecimalSeparator($accountId),
        ];
    }

    public function format(float|int|string $amount, ?int $accountId = null, ?int $forcedDecimals = null): string
    {
        $decimals = $forcedDecimals ?? $this->priceDecimals($accountId);
        $decimals = max(0, min(3, $decimals));

        return number_format(
            (float) $amount,
            $decimals,
            $this->priceDecimalSeparator($accountId),
            $this->priceThousandsSeparator($accountId),
        );
    }

    /**
     * Formatted amount followed by ISO currency code (e.g. "35 000,00 ARS").
     */
    public function formatWithCurrency(
        float|int|string|null $amount,
        Currency|null $currency = null,
        ?string $currencyCode = null,
        ?int $accountId = null,
    ): string {
        if ($amount === null || $amount === '') {
            return '—';
        }

        $code = $currencyCode ?? $currency?->currency_code ?? '';
        $formatted = $this->format($amount, $accountId);

        if ($code !== '' && $code !== '—') {
            return $formatted.' '.$code;
        }

        return $formatted;
    }

    public function normalizeNumericInput(mixed $value, ?int $accountId = null): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        $normalized = str_replace(' ', '', $raw);
        $normalized = str_replace($this->priceThousandsSeparator($accountId), '', $normalized);
        $normalized = str_replace($this->priceDecimalSeparator($accountId), '.', $normalized);

        if (! preg_match('/^-?\d+(?:\.\d+)?$/', $normalized)) {
            return null;
        }

        return (float) $normalized;
    }
}

