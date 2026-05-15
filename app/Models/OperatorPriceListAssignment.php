<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperatorPriceListAssignment extends Model
{
    protected $table = 'operator_price_list_assignments';

    protected $dont_use_audit = true;

    protected $fillable = [
        'operator_price_list_id',
        'agency_id',
        'adjustment_type',
        'adjustment_value',
        'valid_from',
        'valid_to',
        'is_active',
    ];

    protected $casts = [
        'adjustment_value' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(OperatorPriceList::class, 'operator_price_list_id');
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'agency_id');
    }
}
