<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceGastronomyCuisine extends Model
{
    use HasFactory, AuditTrait;

    protected $table = 'cat_service_gastronomy_cuisines';

    protected $fillable = [
        'code',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the translations for this cuisine (one per language).
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ServiceGastronomyCuisineTranslation::class);
    }

    /**
     * Get name for display (from translations).
     */
    public function getNameAttribute(): string
    {
        return $this->getTranslationForDisplay()?->name ?? '';
    }

    /**
     * Translation to use for display. Prefers the translation for the current app locale when available.
     */
    protected function getTranslationForDisplay(): ?ServiceGastronomyCuisineTranslation
    {
        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $translations = $this->translations;

        if ($translations->isEmpty()) {
            return null;
        }

        $locale = app()->getLocale();
        foreach ($translations as $translation) {
            $lang = $translation->language;
            if (! $lang) {
                continue;
            }
            $lang->loadMissing('locale');
            if (Locale::primaryTagMatches($lang->locale, $locale)) {
                return $translation;
            }
        }

        return $translations->first();
    }

    /**
     * Scope to order by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
