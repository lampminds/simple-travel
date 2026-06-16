<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceVariantAvailabilityRule extends Model
{
    use AuditTrait;

    protected $table = 'service_variant_availability_rules';

    protected $fillable = [
        'service_variant_id',
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

    public function serviceVariant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class);
    }

    public function timeSlots(): HasMany
    {
        return $this->hasMany(ServiceVariantAvailabilityTimeSlot::class, 'service_variant_availability_rule_id')
            ->orderByRaw('COALESCE(sort_order, 9999)')
            ->orderBy('id');
    }
}
