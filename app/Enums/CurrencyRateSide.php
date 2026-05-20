<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Which leg of a currency_rates row to use (1 USD = X units in that currency).
 */
enum CurrencyRateSide: string
{
    case Buy = 'buy';

    case Sell = 'sell';
}
