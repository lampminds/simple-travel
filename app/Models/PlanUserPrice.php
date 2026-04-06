<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class PlanUserPrice extends Model
{
    use AuditTrait;

    protected $table = 'plan_user_prices';

    protected $fillable = [
        'up_to',
    ];

    protected $casts = [
        'up_to' => 'integer',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(PlanUserPriceTranslation::class);
    }

    /**
     * Price for display (from translations, current locale preferred).
     */
    public function getPriceAttribute(): ?string
    {
        $t = $this->getTranslationForDisplay();

        return $t !== null && $t->price !== null ? (string) $t->price : null;
    }

    /**
     * Description for display (from translations).
     */
    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->description;
    }

    protected function getTranslationForDisplay(): ?PlanUserPriceTranslation
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
