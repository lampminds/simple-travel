<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceBookingFieldAssignment extends Model
{
    use AuditTrait;

    protected $fillable = [
        'service_id',
        'provider_booking_field_id',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(ProviderBookingField::class, 'provider_booking_field_id');
    }
}
