<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class PriceList extends Model
{
    use AuditTrait;

    protected $table = 'price_lists';

    protected $fillable = [
        'owner_type',
        'owner_id',
        'name',
        'currency_id',
        'valid_from',
        'valid_to',
        'is_active',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_active' => 'boolean',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PriceListItem::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(PriceListAssignment::class);
    }
}
