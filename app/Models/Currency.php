<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

/**
 * Project currency row in cat_currencies; links to master data (lmp_currencies on addons).
 * Exchange rates are stored in currency_rates and filled by an external process.
 */
class Currency extends Model
{
    use AuditTrait;

    protected $table = 'cat_currencies';

    protected $fillable = [
        'currency_id',
    ];

    /**
     * Reference to the master currency data (addons / lmp_currencies).
     */
    public function lmpCurrency(): BelongsTo
    {
        return $this->belongsTo(LmpCurrency::class, 'currency_id');
    }

    /**
     * Exchange rates vs USD (managed by external process; read-only in admin).
     */
    public function currencyRates(): HasMany
    {
        return $this->hasMany(CurrencyRate::class);
    }

    /**
     * Display name for dropdowns and tables (from lmp_currencies when available).
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->relationLoaded('lmpCurrency') && $this->lmpCurrency) {
            return $this->formatDisplay($this->lmpCurrency);
        }

        $lmp = $this->lmpCurrency;
        if ($lmp) {
            return $this->formatDisplay($lmp);
        }

        return "Currency #{$this->id}";
    }

    private function formatDisplay(LmpCurrency $lmp): string
    {
        $parts = array_filter([$lmp->code, $lmp->symbol, $lmp->name]);

        return implode(' — ', $parts) ?: "Currency #{$this->id}";
    }
}
