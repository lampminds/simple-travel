<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class PriceList extends Model
{
    use AuditTrait, HasUuid;

    protected $table = 'provider_price_lists';

    protected $fillable = [
        'provider_id',
        'name',
        'currency_id',
        'valid_from',
        'valid_to',
        'is_active',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'provider_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PriceListItem::class, 'provider_price_list_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(PriceListAssignment::class, 'provider_price_list_id');
    }
}
