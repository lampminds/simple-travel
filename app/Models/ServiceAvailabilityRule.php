<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceAvailabilityRule extends Model
{
    use AuditTrait;

    protected $table = 'service_availability_rules';

    protected $fillable = [
        'service_id',
        'start_date',
        'end_date',
        'weekday_mask',
        'active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'weekday_mask' => 'integer',
        'active' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
