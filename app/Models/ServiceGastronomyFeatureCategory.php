<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceGastronomyFeatureCategory extends Model
{
    use HasFactory, AuditTrait;

    /** Shared with {@see ServiceFeatureCategory} (single taxonomy table). */
    protected $table = 'cat_service_feature_categories';

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
     * Translations (one row per language).
     */
    public function translations(): HasMany
    {
        return $this->hasMany(
            ServiceFeatureCategoryTranslation::class,
            'service_feature_category_id'
        );
    }

    /**
     * Gastronomy features in this category.
     */
    public function serviceGastronomyFeatures(): HasMany
    {
        return $this->hasMany(ServiceGastronomyFeature::class, 'service_feature_category_id');
    }

    /**
     * Display name from translations (current locale when possible).
     */
    public function getNameAttribute(): string
    {
        return $this->getTranslationForDisplay()?->name ?? '';
    }

    /**
     * Prefer translation matching app locale; otherwise first translation.
     */
    protected function getTranslationForDisplay(): ?ServiceFeatureCategoryTranslation
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
     * Order by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
