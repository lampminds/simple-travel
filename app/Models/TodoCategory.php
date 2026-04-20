<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class TodoCategory extends Model
{
    use AuditTrait;

    protected $table = 'todo_categories';

    protected static function booted(): void
    {
        static::deleting(function (TodoCategory $category): void {
            $category->translations()->delete();
        });
    }

    protected $fillable = [
        'code',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(TodoCategoryTranslation::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(TodoTask::class, 'todo_category_id');
    }

    public function getNameAttribute(): string
    {
        $fromTranslation = $this->firstFilledTranslationName();
        if ($fromTranslation !== '') {
            return $fromTranslation;
        }

        return trim((string) ($this->attributes['code'] ?? ''));
    }

    /**
     * Label for tables and selects. Never returns an empty string so Filament does not treat the cell as unset.
     */
    public function displayLabel(): string
    {
        $label = $this->getNameAttribute();

        return $label !== '' ? $label : '—';
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->description;
    }

    /**
     * First non-empty translation name for the current locale resolution, or any translation.
     */
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

    protected function getTranslationForDisplay(): ?TodoCategoryTranslation
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
