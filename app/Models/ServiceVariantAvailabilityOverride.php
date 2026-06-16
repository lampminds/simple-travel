<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceVariantAvailabilityOverride extends Model
{
    use AuditTrait;

    protected $table = 'service_variant_availability_overrides';

    protected $fillable = [
        'service_variant_id',
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

    public function serviceVariant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class);
    }
}
