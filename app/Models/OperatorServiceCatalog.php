<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class OperatorServiceCatalog extends Model
{
    use AuditTrait, HasUuid;

    protected $table = 'operator_service_catalog';

    protected $fillable = [
        'operator_id',
        'status',
        'is_featured',
        'is_public',
        'inventory_type',
        'inventory_total',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
        'inventory_total' => 'integer',
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'operator_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(OperatorServiceCatalogTranslation::class, 'operator_service_catalog_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OperatorPackageItem::class, 'operator_service_catalog_id');
    }

    public function conditionOverrides(): HasMany
    {
        return $this->hasMany(OperatorPackageConditionOverride::class, 'operator_package_id');
    }

    public function packageOffers(): HasMany
    {
        return $this->hasMany(PackageOffer::class, 'operator_service_catalog_id');
    }

    public function availabilityRules(): HasMany
    {
        return $this->hasMany(OperatorPackageAvailabilityRule::class, 'operator_service_catalog_id')
            ->orderByDesc('active')
            ->orderByDesc('start_date');
    }

    public function availabilityOverrides(): HasMany
    {
        return $this->hasMany(OperatorPackageAvailabilityOverride::class, 'operator_service_catalog_id')
            ->orderByDesc('date');
    }

    public function packageAllocations(): HasMany
    {
        return $this->hasMany(PackageAllocation::class, 'operator_service_catalog_id');
    }

    public function usesTimeSlotInventory(): bool
    {
        return in_array($this->inventory_type, ['per_timeslot', 'per_departure'], true);
    }

    /**
     * Label for admin selects (price lists, etc.) using default-locale translation when available.
     */
    public function displayLabel(?int $languageId = null): string
    {
        $translations = $this->relationLoaded('translations')
            ? $this->translations
            : $this->translations()->with('language.locale')->get();

        if ($languageId !== null) {
            $match = $translations->firstWhere('language_id', $languageId);
            $name = trim((string) ($match?->name ?? ''));
            if ($name !== '') {
                return $name;
            }
        }

        foreach ($translations as $translation) {
            $name = trim((string) ($translation->name ?? ''));
            if ($name !== '') {
                return $name;
            }
        }

        return '#'.$this->id;
    }
}
