<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceGastronomy extends Model
{
    use AuditTrait;

    protected $table = 'service_gastronomies';

    protected $fillable = [
        'service_id',
        'city_id',
        'address',
        'latitude',
        'longitude',
        'is_indoor',
        'is_outdoor',
        'has_takeaway',
        'has_delivery',
    ];

    protected $casts = [
        'is_indoor' => 'boolean',
        'is_outdoor' => 'boolean',
        'has_takeaway' => 'boolean',
        'has_delivery' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Catalogue gastronomy types assigned to this profile (many-to-many via pivot).
     */
    public function gastronomyTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            ServiceGastronomyType::class,
            'service_gastronomy_type_assignments',
            'service_gastronomy_id',
            'service_gastronomy_type_id',
        )->withTimestamps();
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(LmpCity::class, 'city_id');
    }

    public function cuisines(): BelongsToMany
    {
        return $this->belongsToMany(
            ServiceGastronomyCuisine::class,
            'service_cuisine_gastronomy_assignments',
            'service_gastronomy_id',
            'service_gastronomy_cuisine_id',
        )->withTimestamps();
    }

    public function venues(): BelongsToMany
    {
        return $this->belongsToMany(
            ServiceGastronomyVenue::class,
            'service_gastronomy_venue_assignments',
            'service_gastronomy_id',
            'service_gastronomy_venue_id',
        )->withTimestamps();
    }

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(
            ServiceGastronomyMenu::class,
            'service_gastronomy_menu_assignments',
            'service_gastronomy_id',
            'service_gastronomy_menu_id',
        )->withTimestamps();
    }

    public function experience(): HasOne
    {
        return $this->hasOne(ServiceGastronomyExperience::class, 'service_gastronomy_id');
    }
}
