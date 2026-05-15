<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceTransferPrice extends Model
{
    use AuditTrait;

    public const PRICING_PER_VEHICLE = 'per_vehicle';

    public const PRICING_PER_PERSON = 'per_person';

    protected $table = 'service_transfer_prices';

    protected $fillable = [
        'service_transfer_id',
        'route_id',
        'service_transfer_vehicle_type_id',
        'pricing_type',
        'currency_id',
        'base_price',
        'price_per_person',
        'price_per_extra_passenger',
        'min_passengers',
        'max_passengers',
        'valid_from',
        'valid_to',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'price_per_person' => 'decimal:2',
        'price_per_extra_passenger' => 'decimal:2',
        'min_passengers' => 'integer',
        'max_passengers' => 'integer',
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    public function serviceTransfer(): BelongsTo
    {
        return $this->belongsTo(ServiceTransfer::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(ServiceTransferRoute::class, 'route_id');
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(ServiceTransferVehicleType::class, 'service_transfer_vehicle_type_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
