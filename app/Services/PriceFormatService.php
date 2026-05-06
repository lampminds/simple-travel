<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Controllers\ParameterReaderController;

final class PriceFormatService
{
    public const CODE_PRICE_DECIMALS = 'price_decimals';

    public const CODE_PRICE_THOUSANDS_SEPARATOR = 'price_thousands_separator';

    public function __construct(private readonly ParameterReaderController $parameterReader)
    {
    }

    /**
     * @return array{decimals:int, thousands_separator:string, decimal_separator:string}
     */
    public function resolveSettings(?int $accountId = null): array
    {
        $decimals = $this->parameterReader->getInt(self::CODE_PRICE_DECIMALS, $accountId, 2);
        $decimals = max(0, min(3, $decimals));

        $thousands = trim((string) $this->parameterReader->getRawValue(self::CODE_PRICE_THOUSANDS_SEPARATOR, $accountId));
        if (! in_array($thousands, [',', '.'], true)) {
            $thousands = ',';
        }

        return [
            'decimals' => $decimals,
            'thousands_separator' => $thousands,
            'decimal_separator' => $thousands === ',' ? '.' : ',',
        ];
    }

    public function format(float|int|string $amount, ?int $accountId = null, ?int $forcedDecimals = null): string
    {
        $settings = $this->resolveSettings($accountId);
        $decimals = $forcedDecimals ?? $settings['decimals'];
        $decimals = max(0, min(3, $decimals));

        return number_format(
            (float) $amount,
            $decimals,
            $settings['decimal_separator'],
            $settings['thousands_separator']
        );
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

        $settings = $this->resolveSettings($accountId);
        $normalized = str_replace(' ', '', $raw);
        $normalized = str_replace($settings['thousands_separator'], '', $normalized);
        $normalized = str_replace($settings['decimal_separator'], '.', $normalized);

        if (! preg_match('/^-?\d+(?:\.\d+)?$/', $normalized)) {
            return null;
        }

        return (float) $normalized;
    }
}

