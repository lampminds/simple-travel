<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class PriceListItem extends Model
{
    protected $table = 'provider_price_list_items';

    /** Table has no audit columns (created_by/updated_by). */
    protected $dont_use_audit = true;

    public $timestamps = false;

    protected $fillable = [
        'provider_price_list_id',
        'service_variant_id',
        'price',
        'pricing_mode',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class, 'provider_price_list_id');
    }

    public function serviceVariant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class);
    }

    public function service(): HasOneThrough
    {
        return $this->hasOneThrough(
            Service::class,
            ServiceVariant::class,
            'id',
            'id',
            'service_variant_id',
            'service_id',
        );
    }
}
