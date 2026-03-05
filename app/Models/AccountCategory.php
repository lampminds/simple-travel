<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class AccountCategory extends Model
{
    use HasFactory, AuditTrait;

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

    /**
     * Get the translations for this category (one per language).
     */
    public function translations(): HasMany
    {
        return $this->hasMany(AccountCategoryTranslation::class);
    }

    /**
     * Get the accounts that belong to this category.
     */
    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'account_category');
    }

    /**
     * Get name for display (from translations).
     */
    public function getNameAttribute(): string
    {
        return $this->getTranslationForDisplay()?->name ?? '';
    }

    /**
     * Get code for display (from main table).
     */
    public function getCodeAttribute(): ?string
    {
        return $this->attributes['code'] ?? null;
    }

    /**
     * Get description for display (current locale translation or first available).
     */
    public function getDescriptionAttribute(): ?string
    {
        $translation = $this->getTranslationForDisplay();

        return $translation?->description;
    }

    /**
     * Translation to use for display (e.g. in tables and dropdowns).
     * Prefers the translation for the current app locale when available.
     */
    protected function getTranslationForDisplay(): ?AccountCategoryTranslation
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

    /**
     * Scope to order by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Scope to filter by group.
     */
    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}
