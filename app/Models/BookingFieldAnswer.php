<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class BookingFieldAnswer extends Model
{
    use AuditTrait;

    protected $fillable = [
        'booking_id',
        'provider_booking_field_id',
        'passenger_index',
        'label_snapshot',
        'input_type_snapshot',
        'scope_snapshot',
        'value',
    ];

    protected $casts = [
        'passenger_index' => 'integer',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(ProviderBookingField::class, 'provider_booking_field_id');
    }
}
