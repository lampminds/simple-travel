<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class OperatorPackageAvailabilityRule extends Model
{
    use AuditTrait;

    protected $table = 'operator_package_availability_rules';

    protected $fillable = [
        'operator_service_catalog_id',
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

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(OperatorServiceCatalog::class, 'operator_service_catalog_id');
    }

    public function timeSlots(): HasMany
    {
        return $this->hasMany(OperatorPackageAvailabilityTimeSlot::class, 'operator_package_availability_rule_id')
            ->orderByRaw('COALESCE(sort_order, 9999)')
            ->orderBy('id');
    }
}
