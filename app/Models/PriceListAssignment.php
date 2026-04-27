<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PriceListAssignment extends Model
{
    protected $table = 'price_list_assignments';

    /** Table has no created_by/updated_by columns. */
    protected $dont_use_audit = true;

    protected $fillable = [
        'price_list_id',
        'assigned_to_type',
        'assigned_to_id',
        'adjustment_type',
        'adjustment_value',
        'valid_from',
        'valid_to',
        'is_active',
    ];

    protected $casts = [
        'adjustment_value' => 'decimal:2',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_active' => 'boolean',
    ];

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class);
    }

    public function assignedTo(): MorphTo
    {
        return $this->morphTo();
    }
}
