<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class CatFaq extends Model
{
    use AuditTrait;

    protected $table = 'cat_faqs';

    protected static function booted(): void
    {
        static::deleting(function (CatFaq $faq): void {
            $faq->translations()->delete();
        });
    }

    protected $fillable = [
        'code',
        'account_type_id',
        'sort_order',
        'active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'account_type_id' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class, 'account_type_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CatFaqTranslation::class, 'faq_id');
    }

    public function getQuestionAttribute(): string
    {
        $translation = $this->getTranslationForDisplay();
        if ($translation && filled($translation->question)) {
            return trim((string) $translation->question);
        }

        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
        }

        foreach ($this->translations as $row) {
            if (filled($row->question)) {
                return trim((string) $row->question);
            }
        }

        return trim((string) ($this->attributes['code'] ?? ''));
    }

    public function getAnswerAttribute(): ?string
    {
        $translation = $this->getTranslationForDisplay();

        return $translation?->answer;
    }

    protected function getTranslationForDisplay(): ?CatFaqTranslation
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
