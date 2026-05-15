<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceTransferLocationType extends Model
{
    use AuditTrait;

    protected $table = 'service_transfer_location_types';

    protected $fillable = [
        'service_transfer_location_type_category_id',
        'code',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceTransferLocationTypeCategory::class, 'service_transfer_location_type_category_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ServiceTransferLocationTypeTranslation::class, 'service_transfer_location_type_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function getNameAttribute(): string
    {
        if (! $this->relationLoaded('translations')) {
            $this->load('translations.language.locale');
        }

        $locale = app()->getLocale();
        foreach ($this->translations as $translation) {
            $lang = $translation->language;
            if ($lang && $lang->relationLoaded('locale') === false) {
                $lang->load('locale');
            }
            if ($lang?->locale && Locale::primaryTagMatches($lang->locale, $locale)) {
                return (string) ($translation->name ?? '');
            }
        }

        return (string) ($this->translations->first()?->name ?? $this->code ?? '');
    }
}
