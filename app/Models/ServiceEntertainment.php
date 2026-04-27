<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceEntertainment extends Model
{
    use HasFactory, AuditTrait;

    protected $table = 'service_entertainment';

    protected $fillable = [
        'service_id',
        'service_entertainment_type_id',
        'difficulty_level',
        'min_age',
        'max_age',
        'guide_included',
        'transport_included',
        'outdoor_activity',
        'max_altitude_m',
        'distance_km',
        'requires_good_weather',
        'active',
    ];

    protected $casts = [
        'guide_included' => 'boolean',
        'transport_included' => 'boolean',
        'outdoor_activity' => 'boolean',
        'requires_good_weather' => 'boolean',
        'active' => 'boolean',
        'difficulty_level' => 'integer',
        'min_age' => 'integer',
        'max_age' => 'integer',
        'max_altitude_m' => 'integer',
        'distance_km' => 'integer',
    ];

    /**
     * Get the service this entertainment belongs to (1:1).
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the entertainment type.
     */
    public function serviceEntertainmentType(): BelongsTo
    {
        return $this->belongsTo(ServiceEntertainmentType::class);
    }
}

