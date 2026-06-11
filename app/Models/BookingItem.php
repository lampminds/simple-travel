<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class BookingItem extends Model
{
    use AuditTrait;

    protected $fillable = [
        'booking_id',
        'operator_package_item_id',
        'status_id',
        'start_date',
        'end_date',
        'quantity',
        'price',
        'currency_id',
        'confirmation_code',
        'provider_reference',
        'package_snapshot',
        'discount',
        'total',
        'remarks',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'package_snapshot' => 'array',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'remarks' => 'array',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function packageItem(): BelongsTo
    {
        return $this->belongsTo(OperatorPackageItem::class, 'operator_package_item_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(CatBookingStatus::class, 'status_id');
    }
}
