<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceTransfer extends Model
{
    use AuditTrait;

    public const TRANSFER_ONE_WAY = 'one_way';

    public const TRANSFER_ROUND_TRIP = 'round_trip';

    public const MODALITY_PRIVATE = 'private';

    public const MODALITY_SHARED = 'shared';

    public const OPERATION_ON_DEMAND = 'on_demand';

    public const OPERATION_SCHEDULED = 'scheduled';

    protected $table = 'service_transfers';

    protected $fillable = [
        'service_variant_id',
        'transfer_type',
        'modality',
        'allows_multiple_stops',
        'operation_mode',
        'duration_minutes',
        'default_duration_minutes',
        'requires_flight_info',
        'requires_pickup_time',
        'requires_dropoff_time',
    ];

    protected $casts = [
        'allows_multiple_stops' => 'boolean',
        'duration_minutes' => 'integer',
        'default_duration_minutes' => 'integer',
        'requires_flight_info' => 'boolean',
        'requires_pickup_time' => 'boolean',
        'requires_dropoff_time' => 'boolean',
    ];

    public function serviceVariant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class);
    }

    public function routes(): HasMany
    {
        return $this->hasMany(ServiceTransferRoute::class)->orderBy('id');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(ServiceTransferVehicle::class)->orderBy('id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ServiceTransferPrice::class)->orderBy('id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ServiceTransferSchedule::class)->orderBy('id');
    }
}
