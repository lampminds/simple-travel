<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceTransferLocation extends Model
{
    use AuditTrait;

    protected $table = 'service_transfer_locations';

    protected $fillable = [
        'account_id',
        'service_transfer_location_type_id',
        'address',
        'city_id',
        'slug',
        'latitude',
        'longitude',
        'airport_code',
        'parent_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function locationType(): BelongsTo
    {
        return $this->belongsTo(ServiceTransferLocationType::class, 'service_transfer_location_type_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(LmpCity::class, 'city_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ServiceTransferLocationTranslation::class, 'service_transfer_location_id');
    }

    /**
     * Translated display name for the current app locale (from {@see translations}).
     */
    public function getNameAttribute(): string
    {
        if (! $this->relationLoaded('translations')) {
            $this->load('translations.language.locale');
        }

        $locale = app()->getLocale();
        foreach ($this->translations as $translation) {
            $lang = $translation->language;
            if ($lang) {
                $lang->loadMissing('locale');
            }
            if ($lang?->locale && Locale::primaryTagMatches($lang->locale, $locale)) {
                $name = trim((string) ($translation->name ?? ''));
                if ($name !== '') {
                    return $name;
                }
            }
        }

        $fallback = trim((string) ($this->translations->first()?->name ?? ''));

        return $fallback !== '' ? $fallback : ('#'.$this->id);
    }

    /**
     * Label for selects (name for current locale, then airport / city hints).
     */
    public function getWizardLabelAttribute(): string
    {
        return $this->resolveWizardLabel(includeCityHint: true);
    }

    /**
     * Label for route origin/destination selects when the service city is already known (omit city suffix).
     */
    public function wizardRouteSelectLabel(): string
    {
        return $this->resolveWizardLabel(includeCityHint: false);
    }

    private function resolveWizardLabel(bool $includeCityHint): string
    {
        $name = $this->name;

        if ($includeCityHint) {
            $this->loadMissing('city');
        }

        $hints = array_filter([
            $this->airport_code ? strtoupper((string) $this->airport_code) : null,
            $includeCityHint && $this->city ? $this->city->name : null,
        ]);

        $suffix = $hints !== [] ? ' ('.implode(' · ', $hints).')' : '';

        return trim($name.$suffix) !== '' ? trim($name.$suffix) : ('#'.$this->id);
    }
}
