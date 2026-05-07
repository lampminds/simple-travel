<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class Plan extends Model
{
    use AuditTrait;

    protected $table = 'plans';

    protected $fillable = [
        'code',
        'sort_order',
        'usd_price',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
        'usd_price' => 'decimal:2',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(PlanTranslation::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PlanItem::class)->whereNull('parent_id')->orderBy('sort_order');
    }

    /**
     * All items (root and nested) for this plan.
     */
    public function allItems(): HasMany
    {
        return $this->hasMany(PlanItem::class)->orderBy('sort_order');
    }

    /**
     * Name for display (from translations, current locale preferred).
     */
    public function getNameAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->name;
    }

    /**
     * Price for display in USD (stored in plans table).
     */
    public function getPriceAttribute(): ?string
    {
        return $this->usd_price !== null ? (string) $this->usd_price : null;
    }

    /**
     * Description for display (from translations).
     */
    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->description;
    }

    protected function getTranslationForDisplay(): ?PlanTranslation
    {
        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
        }
        if ($this->translations->isEmpty()) {
            return null;
        }
        $locale = app()->getLocale();
        foreach ($this->translations as $translation) {
            $lang = $translation->language;
            if (! $lang) {
                continue;
            }
            $lang->loadMissing('locale');
            if (Locale::primaryTagMatches($lang->locale, $locale)) {
                return $translation;
            }
        }

        return $this->translations->first();
    }
}
