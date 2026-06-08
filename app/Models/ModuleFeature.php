<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ModuleFeature extends Model
{
    use AuditTrait;

    protected $fillable = [
        'module_id',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'active' => 'boolean',
    ];

    public function getTextAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->text;
    }

    protected function getTranslationForDisplay(): ?ModuleFeatureTranslation
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

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ModuleFeatureTranslation::class);
    }
}
