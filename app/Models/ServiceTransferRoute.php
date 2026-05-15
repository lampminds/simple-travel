<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceTransferRoute extends Model
{
    use AuditTrait;

    protected $table = 'service_transfer_routes';

    protected $fillable = [
        'service_transfer_id',
        'origin_location_id',
        'destination_location_id',
        'is_active',
        'distance_km',
        'duration_minutes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'distance_km' => 'decimal:2',
        'duration_minutes' => 'integer',
    ];

    public function serviceTransfer(): BelongsTo
    {
        return $this->belongsTo(ServiceTransfer::class);
    }

    public function origin(): BelongsTo
    {
        return $this->belongsTo(ServiceTransferLocation::class, 'origin_location_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(ServiceTransferLocation::class, 'destination_location_id');
    }
}
