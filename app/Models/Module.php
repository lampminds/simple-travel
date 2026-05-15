<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class Module extends Model
{
    use AuditTrait;

    protected $fillable = [
        'code',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'active' => 'boolean',
    ];

    public function getNameAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->name;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->description;
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ModuleTranslation::class);
    }

    protected function getTranslationForDisplay(): ?ModuleTranslation
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

    public function features(): HasMany
    {
        return $this->hasMany(ModuleFeature::class)->orderBy('sort_order');
    }

    public function commercialPlanModules(): HasMany
    {
        return $this->hasMany(CommercialPlanModule::class);
    }

    public function commercialModulePrices(): HasMany
    {
        return $this->hasMany(CommercialModulePrice::class);
    }

    public function commercialSubscriptionModules(): HasMany
    {
        return $this->hasMany(CommercialSubscriptionModule::class);
    }
}
