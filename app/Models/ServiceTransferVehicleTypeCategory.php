<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceTransferVehicleTypeCategory extends Model
{
    use AuditTrait, HasFactory;

    protected $table = 'cat_service_transfer_vehicle_type_categories';

    protected $fillable = [
        'code',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(ServiceTransferVehicleTypeCategoryTranslation::class, 'service_transfer_vehicle_type_category_id');
    }

    public function vehicleTypes(): HasMany
    {
        return $this->hasMany(ServiceTransferVehicleType::class, 'service_transfer_vehicle_type_category_id');
    }

    public function getNameAttribute(): string
    {
        return $this->getTranslationForDisplay()?->name ?? '';
    }

    protected function getTranslationForDisplay(): ?ServiceTransferVehicleTypeCategoryTranslation
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
}
