<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class CatBookingStatus extends Model
{
    use AuditTrait;

    public const TYPE_MAIN = 'main';

    public const TYPE_ITEM = 'item';

    protected $table = 'cat_booking_statuses';

    protected static function booted(): void
    {
        static::deleting(function (CatBookingStatus $status): void {
            $status->translations()->delete();
        });
    }

    protected $fillable = [
        'type',
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
        return $this->hasMany(CatBookingStatusTranslation::class, 'status_id');
    }

    public function getNameAttribute(): string
    {
        $fromTranslation = $this->firstFilledTranslationName();
        if ($fromTranslation !== '') {
            return $fromTranslation;
        }

        return trim((string) ($this->attributes['code'] ?? ''));
    }

    public function displayLabel(): string
    {
        $label = $this->getNameAttribute();

        return $label !== '' ? $label : '—';
    }

    public function getHelpTipAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->help_tip;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->description;
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

    protected function getTranslationForDisplay(): ?CatBookingStatusTranslation
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

    /**
     * @return array<string, string>
     */
    public static function typeOptions(): array
    {
        return [
            self::TYPE_MAIN => __('filament.resources.cat_booking_status_type.main'),
            self::TYPE_ITEM => __('filament.resources.cat_booking_status_type.item'),
        ];
    }
}
