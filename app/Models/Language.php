<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class Language extends Model
{
    use AuditTrait;

    protected $fillable = [
        'language_id',
    ];

    /**
     * Reference to the master language data (e.g. on addons / lmp_languages).
     * Uses App\Models\LmpLanguage so the addons connection is applied.
     */
    public function lmpLanguage(): BelongsTo
    {
        return $this->belongsTo(LmpLanguage::class, 'language_id');
    }

    /**
     * Display name for dropdowns and tables (from lmp_languages when available).
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->relationLoaded('lmpLanguage') && $this->lmpLanguage) {
            return $this->lmpLanguage->name ?? $this->lmpLanguage->code ?? "Language #{$this->id}";
        }

        $lmp = $this->lmpLanguage;
        if ($lmp) {
            return $lmp->name ?? $lmp->code ?? "Language #{$this->id}";
        }

        return "Language #{$this->id}";
    }
}
