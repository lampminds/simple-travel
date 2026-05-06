<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ContactType extends Model
{
    use HasFactory, AuditTrait;

    protected $table = 'cat_contact_types';

    protected $fillable = [
        'code',
        'is_unique_per_person',
        'mask',
        'validation',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'is_unique_per_person' => 'boolean',
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
     * Values stored on persons for this channel type (email, phone, etc.).
     */
    public function personContactMethods(): HasMany
    {
        return $this->hasMany(PersonContactMethod::class, 'contact_type_id');
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
            $lang->loadMissing('locale');
            if (Locale::primaryTagMatches($lang->locale, $locale)) {
                return $translation;
            }
        }

        return $translations->first();
    }
}
