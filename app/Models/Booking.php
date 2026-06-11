<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class Booking extends Model
{
    use AuditTrait, HasUuid;

    protected $fillable = [
        'booking_code',
        'operator_id',
        'agency_id',
        'package_offer_id',
        'organization_id',
        'invitation_token',
        'status_id',
        'booking_source',
        'travel_start_date',
        'travel_end_date',
        'passengers_snapshot',
        'subtotal',
        'currency_id',
        'billing_type',
        'billing_person_id',
        'billing_organization_id',
        'remarks_internal',
        'remarks_customer',
    ];

    protected $casts = [
        'travel_start_date' => 'date',
        'travel_end_date' => 'date',
        'passengers_snapshot' => 'array',
        'subtotal' => 'decimal:2',
        'remarks_internal' => 'array',
        'remarks_customer' => 'array',
    ];

    public function operatorAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'operator_id');
    }

    public function agencyAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'agency_id');
    }

    public function packageOffer(): BelongsTo
    {
        return $this->belongsTo(PackageOffer::class, 'package_offer_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(CatBookingStatus::class, 'status_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class, 'booking_id');
    }
}
