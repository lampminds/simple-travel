<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperatorPriceListItem extends Model
{
    protected $table = 'operator_price_list_items';

    protected $dont_use_audit = true;

    public $timestamps = false;

    protected $fillable = [
        'operator_price_list_id',
        'operator_service_catalog_id',
        'price',
        'pricing_mode',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(OperatorPriceList::class, 'operator_price_list_id');
    }

    public function catalogEntry(): BelongsTo
    {
        return $this->belongsTo(OperatorServiceCatalog::class, 'operator_service_catalog_id');
    }
}
