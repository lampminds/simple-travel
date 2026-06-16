<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class OperatorPriceList extends Model
{
    use AuditTrait, HasUuid;

    protected $table = 'operator_price_lists';

    protected $fillable = [
        'operator_id',
        'name',
        'currency_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'operator_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OperatorPriceListItem::class, 'operator_price_list_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(OperatorPriceListAssignment::class, 'operator_price_list_id');
    }
}
