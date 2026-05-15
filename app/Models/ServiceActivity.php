<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceActivity extends Model
{
    use HasFactory, AuditTrait;

    protected $table = 'service_activities';

    protected $fillable = [
        'service_id',
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

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Catalogue activity types assigned to this profile (many-to-many via pivot).
     */
    public function activityTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            ServiceActivityType::class,
            'service_activity_type_assignments',
            'service_activity_id',
            'service_activity_type_id'
        )->withTimestamps();
    }
}
