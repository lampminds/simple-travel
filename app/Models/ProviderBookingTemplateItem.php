<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ProviderBookingTemplateItem extends Model
{
    use AuditTrait;

    protected $fillable = [
        'provider_booking_template_id',
        'provider_booking_field_id',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'provider_booking_field_id' => 'integer',
        'is_required' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(ProviderBookingTemplate::class, 'provider_booking_template_id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(ProviderBookingField::class, 'provider_booking_field_id');
    }
}
