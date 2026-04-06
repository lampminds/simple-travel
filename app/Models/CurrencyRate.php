<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

/**
 * Exchange rate of a project currency vs USD.
 * Populated by an external process; not edited via Filament.
 */
class CurrencyRate extends Model
{
    use AuditTrait;

    protected $table = 'currency_rates';

    protected $fillable = [
        'currency_id',
        'rate',
        'starting_at',
    ];

    protected $casts = [
        'rate' => 'decimal:3',
        'starting_at' => 'datetime',
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
