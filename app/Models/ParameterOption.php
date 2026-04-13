<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ParameterOption extends Model
{
    use AuditTrait;

    protected $table = 'cat_parameter_options';

    /** @var list<string> */
    protected $fillable = [
        'parameter_definition_id',
        'value',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function parameterDefinition(): BelongsTo
    {
        return $this->belongsTo(ParameterDefinition::class, 'parameter_definition_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ParameterOptionTranslation::class, 'parameter_option_id');
    }

    /**
     * Label for UI (current locale or first translation with a name).
     */
    public function labelForDisplay(): string
    {
        $translation = $this->getTranslationForDisplay();

        if ($translation && $translation->name !== null && $translation->name !== '') {
            return $translation->name;
        }

        return (string) $this->value;
    }

    protected function getTranslationForDisplay(): ?ParameterOptionTranslation
    {
        if (! $this->relationLoaded('translations')) {
            $this->load('translations.language.locale');
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
}
