<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class Language extends Model
{
    use AuditTrait;

    protected $table = 'cat_languages';

    protected $fillable = [
        'language_id',
    ];

    /**
     * Reference to the locale catalog (cat_locales).
     */
    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class, 'language_id');
    }

    /**
     * Display name for dropdowns and tables (from cat_locales when available).
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->relationLoaded('locale') && $this->locale) {
            return $this->locale->name_en ?? $this->locale->tag ?? "Language #{$this->id}";
        }

        $loc = $this->locale;
        if ($loc) {
            return $loc->name_en ?? $loc->tag ?? "Language #{$this->id}";
        }

        return "Language #{$this->id}";
    }
}
