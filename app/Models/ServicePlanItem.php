<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServicePlanItem extends Model
{
    use AuditTrait;

    protected $fillable = [
        'service_plan_id',
        'parent_id',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function servicePlan(): BelongsTo
    {
        return $this->belongsTo(ServicePlan::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ServicePlanItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ServicePlanItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ServicePlanItemTranslation::class);
    }

    /**
     * Text for display (from translations, current locale preferred).
     */
    public function getDisplayTextAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->text;
    }

    protected function getTranslationForDisplay(): ?ServicePlanItemTranslation
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
            $lang->loadMissing('lmpLanguage');
            $lmp = $lang->lmpLanguage;
            $code = $lmp ? ($lmp->code ?? $lmp->code2 ?? null) : null;
            if ($code === $locale) {
                return $translation;
            }
        }

        return $this->translations->first();
    }
}
