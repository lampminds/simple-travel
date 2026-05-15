<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceTransferVehicle extends Model
{
    use AuditTrait;

    protected $table = 'service_transfer_vehicles';

    protected $fillable = [
        'service_transfer_id',
        'service_transfer_vehicle_type_id',
        'max_passengers',
        'max_luggage',
        'notes',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'max_passengers' => 'integer',
        'max_luggage' => 'integer',
    ];

    public function serviceTransfer(): BelongsTo
    {
        return $this->belongsTo(ServiceTransfer::class);
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(ServiceTransferVehicleType::class, 'service_transfer_vehicle_type_id');
    }
}
