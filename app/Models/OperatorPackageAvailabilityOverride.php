<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class OperatorPackageAvailabilityOverride extends Model
{
    use AuditTrait;

    protected $table = 'operator_package_availability_overrides';

    protected $fillable = [
        'operator_service_catalog_id',
        'date',
        'start_time',
        'capacity',
        'closed',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
        'capacity' => 'integer',
        'closed' => 'boolean',
    ];

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(OperatorServiceCatalog::class, 'operator_service_catalog_id');
    }
}
