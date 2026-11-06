<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class PassengerCategory extends Model
{
    use AuditTrait;

    protected $fillable = [
        'code',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::deleting(function (PassengerCategory $category): void {
            $category->translations()->delete();
        });
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PassengerCategoryTranslation::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function getNameAttribute(): string
    {
        $translation = $this->translationForDisplay();

        if ($translation !== null && filled($translation->name)) {
            return trim((string) $translation->name);
        }

        return (string) $this->code;
    }

    public function translationForDisplay(): ?PassengerCategoryTranslation
    {
        if (! $this->relationLoaded('translations')) {
            $this->load('translations.language.locale');
        }

        $locale = app()->getLocale();

        foreach ($this->translations as $translation) {
            $language = $translation->language;
            if ($language === null) {
                continue;
            }

            $language->loadMissing('locale');

            if (Locale::primaryTagMatches($language->locale, $locale)) {
                return $translation;
            }
        }

        return $this->translations->first();
    }

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return static::query()
            ->ordered()
            ->with('translations.language.locale')
            ->get()
            ->mapWithKeys(fn (PassengerCategory $category): array => [$category->id => $category->name])
            ->all();
    }
}
