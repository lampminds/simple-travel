<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

/**
 * Project currency row in cat_currencies; links to master data (lmp_currencies on addons).
 * Exchange rates vs USD live in currency_rates (Filament + currency:fetch-dolarapi-rates).
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
     * Exchange rates vs USD (buy/sell legs; USD = 1). System rows have account_id null.
     */
    public function currencyRates(): HasMany
    {
        return $this->hasMany(CurrencyRate::class);
    }

    /**
     * True when this cat_currencies row points to master currency code USD (addons DB).
     *
     * Avoids whereHas across connections: that generates SQL on the project DB and fails
     * when lmp_currencies only exists on the addons connection.
     */
    public static function isUsdProjectCurrency(?int $catCurrencyId): bool
    {
        if ($catCurrencyId === null) {
            return false;
        }

        $masterCurrencyId = static::query()->whereKey($catCurrencyId)->value('currency_id');
        if ($masterCurrencyId === null) {
            return false;
        }

        return LmpCurrency::query()
            ->whereKey($masterCurrencyId)
            ->whereRaw('UPPER(code) = ?', ['USD'])
            ->exists();
    }

    /**
     * Label for selects/lists using only cat_currencies columns (no join to lmp_currencies).
     */
    public function getCatCatalogLabelAttribute(): string
    {
        return __('filament.resources.currency_cat_catalog_label', [
            'id' => $this->id,
            'ref' => $this->currency_id,
        ]);
    }

    /**
     * ISO currency code for amount display (e.g. ARS, USD).
     */
    public function getCurrencyCodeAttribute(): string
    {
        $lmp = $this->relationLoaded('lmpCurrency') ? $this->lmpCurrency : $this->lmpCurrency;
        if ($lmp !== null && trim((string) $lmp->code) !== '') {
            return strtoupper(trim((string) $lmp->code));
        }

        return '—';
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

    /**
     * Resolve a project currency row by ISO code (e.g. ARS, USD).
     */
    public static function resolveByIsoCode(string $code): ?self
    {
        static $cache = [];

        $normalized = strtoupper(trim($code));
        if ($normalized === '' || $normalized === '—') {
            return null;
        }

        if (array_key_exists($normalized, $cache)) {
            return $cache[$normalized];
        }

        $masterId = LmpCurrency::query()
            ->whereRaw('UPPER(code) = ?', [$normalized])
            ->value('id');

        if ($masterId === null) {
            $cache[$normalized] = null;

            return null;
        }

        $currency = static::query()
            ->with('lmpCurrency')
            ->where('currency_id', $masterId)
            ->first();

        $cache[$normalized] = $currency;

        return $currency;
    }

    private function formatDisplay(LmpCurrency $lmp): string
    {
        $parts = array_filter([$lmp->code, $lmp->symbol, $lmp->name]);

        return implode(' — ', $parts) ?: "Currency #{$this->id}";
    }
}
