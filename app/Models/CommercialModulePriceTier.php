<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class CommercialModulePriceTier extends Model
{
    use AuditTrait;

    protected $fillable = [
        'module_price_id',
        'from_users',
        'to_users',
        'price_per_user',
    ];

    protected $casts = [
        'from_users' => 'integer',
        'to_users' => 'integer',
        'price_per_user' => 'decimal:2',
    ];

    public function modulePrice(): BelongsTo
    {
        return $this->belongsTo(CommercialModulePrice::class, 'module_price_id');
    }
}
