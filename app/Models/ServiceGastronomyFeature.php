<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceGastronomyFeature extends Model
{
    use HasFactory, AuditTrait;

    /** Shared with {@see ServiceFeature} (single features table). */
    protected $table = 'cat_service_features';

    protected $fillable = [
        'code',
        'service_feature_category_id',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function serviceGastronomyFeatureCategory(): BelongsTo
    {
        return $this->belongsTo(
            ServiceGastronomyFeatureCategory::class,
            'service_feature_category_id'
        );
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ServiceFeatureTranslation::class, 'service_feature_id');
    }

    public function getNameAttribute(): string
    {
        return $this->getTranslationForDisplay()?->name ?? '';
    }

    protected function getTranslationForDisplay(): ?ServiceFeatureTranslation
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

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
