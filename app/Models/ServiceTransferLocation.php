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
        'service_transfer_location_type_id',
        'address',
        'city_id',
        'latitude',
        'longitude',
        'airport_code',
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
        if (! $this->relationLoaded('translations')) {
            $this->load('translations.language.locale');
        }

        if ($includeCityHint) {
            $this->loadMissing('city');
        }

        $locale = app()->getLocale();
        $name = '';
        foreach ($this->translations as $translation) {
            $lang = $translation->language;
            if ($lang) {
                $lang->loadMissing('locale');
            }
            if ($lang?->locale && Locale::primaryTagMatches($lang->locale, $locale)) {
                $name = (string) ($translation->name ?? '');
                break;
            }
        }
        if ($name === '' && $this->translations->isNotEmpty()) {
            $name = (string) ($this->translations->first()?->name ?? '');
        }

        $hints = array_filter([
            $this->airport_code ? strtoupper((string) $this->airport_code) : null,
            $includeCityHint && $this->city ? $this->city->name : null,
        ]);

        $suffix = $hints !== [] ? ' ('.implode(' · ', $hints).')' : '';

        return trim($name.$suffix) !== '' ? trim($name.$suffix) : ('#'.$this->id);
    }
}
