<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class CommercialPlan extends Model
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

    protected function getTranslationForDisplay(): ?CommercialPlanTranslation
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

    /**
     * Account business types this plan applies to ({@see AccountType}, pivot commercial_plan_account_types).
     */
    public function accountTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            AccountType::class,
            'commercial_plan_account_types',
            'commercial_plan_id',
            'account_type_id',
        );
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CommercialPlanTranslation::class);
    }

    public function commercialPlanModules(): HasMany
    {
        return $this->hasMany(CommercialPlanModule::class)->orderBy('sort_order');
    }

    public function commercialSubscriptions(): HasMany
    {
        return $this->hasMany(CommercialSubscription::class);
    }
}
