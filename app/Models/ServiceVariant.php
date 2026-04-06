<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceVariant extends Model
{
    use AuditTrait;

    protected $table = 'service_variants';

    /** Columns on service_variants (see migration; no rules/overrides/slots here). */
    protected $fillable = [
        'service_id',
        'sku',
        'status',
        'pricing_type',
        'base_price',
        'currency_id',
        'inventory_type',
        'inventory_total',
        'capacity_min',
        'capacity_max',
        'min_advance_booking_hours',
        'max_advance_booking_days',
        'start_time',
        'end_time',
        'sort_order',
    ];

    protected $casts = [
        'capacity_min' => 'integer',
        'capacity_max' => 'integer',
        'base_price' => 'decimal:2',
        'inventory_total' => 'integer',
        'min_advance_booking_hours' => 'integer',
        'max_advance_booking_days' => 'integer',
        'sort_order' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the currency for this variant (cat_currencies).
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
