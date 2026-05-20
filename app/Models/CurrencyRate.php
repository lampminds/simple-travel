<?php

namespace App\Models;

use App\Enums\CurrencyRateSide;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

/**
 * Exchange rate of a project currency vs USD (1 USD = units_per_usd_* in that currency).
 *
 * account_id null = official system rate; non-null = tenant override.
 * USD has no row requirement (always 1). Inactive rows are ignored by {@see \App\Services\CurrencyConversionService}.
 *
 * @see \App\Services\CurrencyConversionService Effective rate for a date = latest active row with starting_at <= that day.
 */
class CurrencyRate extends Model
{
    use AuditTrait;

    protected $table = 'currency_rates';

    protected $fillable = [
        'account_id',
        'currency_id',
        'source',
        'units_per_usd_buy',
        'units_per_usd_sell',
        'starting_at',
        'is_active',
    ];

    protected $casts = [
        'units_per_usd_buy' => 'decimal:8',
        'units_per_usd_sell' => 'decimal:8',
        'starting_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function unitsPerUsd(CurrencyRateSide $side): float
    {
        return $side === CurrencyRateSide::Buy
            ? (float) $this->units_per_usd_buy
            : (float) $this->units_per_usd_sell;
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function isSystemRate(): bool
    {
        return $this->account_id === null;
    }
}
