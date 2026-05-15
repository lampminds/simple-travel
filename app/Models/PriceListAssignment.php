<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceListAssignment extends Model
{
    protected $table = 'provider_price_list_assignments';

    /** Table has no created_by/updated_by columns. */
    protected $dont_use_audit = true;

    protected $fillable = [
        'provider_price_list_id',
        'operator_id',
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
        return $this->belongsTo(PriceList::class, 'provider_price_list_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'operator_id');
    }
}
