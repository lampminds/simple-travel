<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class CatDocument extends Model
{
    use HasFactory, AuditTrait;

    protected $table = 'cat_documents';

    protected static function booted(): void
    {
        static::deleting(function (CatDocument $document): void {
            $document->translations()->delete();
        });
    }

    protected $fillable = [
        'group',
        'code',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(CatDocumentTranslation::class, 'document_id');
    }

    public function getNameAttribute(): string
    {
        return $this->getTranslationForDisplay()?->name ?? '';
    }

    public function getCodeAttribute(): ?string
    {
        return $this->attributes['code'] ?? null;
    }

    public function getDescriptionAttribute(): ?string
    {
        $translation = $this->getTranslationForDisplay();

        return $translation?->description;
    }

    protected function getTranslationForDisplay(): ?CatDocumentTranslation
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

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}
