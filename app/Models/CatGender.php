<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class CatGender extends Model
{
    use AuditTrait, HasFactory;

    protected $table = 'cat_genders';

    protected static function booted(): void
    {
        static::deleting(function (CatGender $gender): void {
            $gender->translations()->delete();
        });
    }

    protected $fillable = [
        'code',
        'active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CatGenderTranslation::class, 'gender_id');
    }

    public function persons(): HasMany
    {
        return $this->hasMany(Person::class, 'gender_id');
    }

    public function getNameAttribute(): string
    {
        $fromTranslation = $this->firstFilledTranslationName();
        if ($fromTranslation !== '') {
            return $fromTranslation;
        }

        return trim((string) ($this->attributes['code'] ?? ''));
    }

    protected function firstFilledTranslationName(): string
    {
        $translation = $this->getTranslationForDisplay();
        if ($translation && filled($translation->name)) {
            return trim((string) $translation->name);
        }

        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
        }

        foreach ($this->translations as $row) {
            if (filled($row->name)) {
                return trim((string) $row->name);
            }
        }

        return '';
    }

    protected function getTranslationForDisplay(): ?CatGenderTranslation
    {
        if (! $this->relationLoaded('translations')) {
            $this->load(['translations.language.locale']);
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

        return $translations->sortBy('language_id')->first();
    }
}
