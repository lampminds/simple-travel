<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceVariantAvailabilityTimeSlot extends Model
{
    use AuditTrait;

    protected $table = 'service_variant_availability_time_slots';

    protected $fillable = [
        'service_variant_availability_rule_id',
        'start_time',
        'end_time',
        'capacity',
        'cutoff_minutes',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'cutoff_minutes' => 'integer',
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(ServiceVariantAvailabilityRule::class, 'service_variant_availability_rule_id');
    }
}
