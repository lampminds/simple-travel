<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ContactType extends Model
{
    use HasFactory, AuditTrait;

    protected $fillable = [
        'code',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the translations for this contact type (one per language).
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ContactTypeTranslation::class);
    }

    /**
     * Get the contacts that use this type.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Get code for display (prefers translation name for current locale, else parent code).
     */
    public function getCodeAttribute(): ?string
    {
        $trans = $this->getTranslationForDisplay();

        return $trans?->name ?? $this->attributes['code'] ?? null;
    }

    /**
     * Get mask for display (from translations).
     */
    public function getMaskAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->mask;
    }

    /**
     * Get validation for display (from translations).
     */
    public function getValidationAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->validation;
    }

    /**
     * Translation to use for display (e.g. in tables and dropdowns).
     * Prefers the translation for the current app locale when available.
     */
    protected function getTranslationForDisplay(): ?ContactTypeTranslation
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
            $lang->loadMissing('lmpLanguage');
            $lmp = $lang->lmpLanguage;
            $code = $lmp ? ($lmp->code ?? $lmp->code2 ?? null) : null;
            if ($code === $locale) {
                return $translation;
            }
        }

        return $translations->first();
    }
}
