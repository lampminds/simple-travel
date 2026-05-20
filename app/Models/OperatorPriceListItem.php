<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperatorPriceListItem extends Model
{
    public const MODE_PERCENTAGE = 'percentage';

    public const MODE_FIXED_DELTA = 'fixed_delta';

    public const MODE_DIRECT = 'direct';

    protected $table = 'operator_price_list_items';

    protected $dont_use_audit = true;

    public $timestamps = false;

    protected $fillable = [
        'operator_price_list_id',
        'operator_package_item_id',
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

    public function packageItem(): BelongsTo
    {
        return $this->belongsTo(OperatorPackageItem::class, 'operator_package_item_id');
    }
}
