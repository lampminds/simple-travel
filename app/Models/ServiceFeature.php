<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceFeature extends Model
{
    use HasFactory, AuditTrait;

    protected static function booted(): void
    {
        static::deleting(function (ServiceFeature $feature): void {
            $feature->translations()->delete();
        });
    }

    protected $table = 'cat_service_features';

    protected $fillable = [
        'code',
        'service_feature_category_id',
        'sort_order',
        'active',
        'is_selectable',
        'parent_id',
    ];

    protected $casts = [
        'active' => 'boolean',
        'is_selectable' => 'boolean',
        'parent_id' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Category this feature belongs to.
     */
    public function serviceFeatureCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceFeatureCategory::class, 'service_feature_category_id');
    }

    /**
     * Parent feature (for hierarchical grouping).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Child features.
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Get translations for this feature (one per language).
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ServiceFeatureTranslation::class, 'service_feature_id');
    }

    /**
     * Get name for display (from translations).
     */
    public function getNameAttribute(): string
    {
        return $this->getTranslationForDisplay()?->name ?? '';
    }

    /**
     * Translation to use for display. Prefers current app locale when available.
     */
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

    /**
     * Scope to order by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}

