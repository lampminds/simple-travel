<?php

declare(strict_types=1);

namespace App\Enums;

enum PriceAdjustmentType: string
{
    case PercentageDiscount = 'percentage_discount';
    case PercentageSurcharge = 'percentage_surcharge';
    case FixedDiscount = 'fixed_discount';
    case FixedSurcharge = 'fixed_surcharge';
    case FixedPrice = 'fixed_price';

    public function label(): string
    {
        return (string) __('account.passenger_policies.adjustment_type.'.$this->value);
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    public function isPercentage(): bool
    {
        return $this === self::PercentageDiscount || $this === self::PercentageSurcharge;
    }

    /**
     * Apply this adjustment to a regular amount. The result is rounded to cents.
     */
    public function apply(float $baseAmount, float $value): float
    {
        $result = match ($this) {
            self::PercentageDiscount => $baseAmount * (1 - ($value / 100)),
            self::PercentageSurcharge => $baseAmount * (1 + ($value / 100)),
            self::FixedDiscount => $baseAmount - $value,
            self::FixedSurcharge => $baseAmount + $value,
            self::FixedPrice => $value,
        };

        return round($result, 2);
    }
}
