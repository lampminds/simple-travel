<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

/**
 * Exchange rate of a project currency vs USD (1 USD = units_per_usd in that currency).
 * USD is always 1. Rows apply from starting_at forward until superseded by a later row.
 */
class CurrencyRate extends Model
{
    use AuditTrait;

    protected $table = 'currency_rates';

    protected $fillable = [
        'currency_id',
        'units_per_usd',
        'starting_at',
    ];

    protected $casts = [
        'units_per_usd' => 'decimal:8',
        'starting_at' => 'datetime',
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
