<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class PlanItem extends Model
{
    use AuditTrait;

    protected $table = 'plan_items';

    protected $fillable = [
        'plan_id',
        'parent_id',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(PlanItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(PlanItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PlanItemTranslation::class);
    }

    /**
     * Text for display (from translations, current locale preferred).
     */
    public function getDisplayTextAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->text;
    }

    protected function getTranslationForDisplay(): ?PlanItemTranslation
    {
        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
        }
        if ($this->translations->isEmpty()) {
            return null;
        }
        $locale = app()->getLocale();
        foreach ($this->translations as $translation) {
            $lang = $translation->language;
            if (! $lang) {
                continue;
            }
            $lang->loadMissing('locale');
            if (Locale::primaryTagMatches($lang->locale, $locale)) {
                return $translation;
            }
        }

        return $this->translations->first();
    }
}
