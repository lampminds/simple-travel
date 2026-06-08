<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceTransferSchedule extends Model
{
    use AuditTrait;

    protected $table = 'service_transfer_schedules';

    protected $fillable = [
        'service_transfer_id',
        'day_of_week',
        'departure_time',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function serviceTransfer(): BelongsTo
    {
        return $this->belongsTo(ServiceTransfer::class);
    }
}
