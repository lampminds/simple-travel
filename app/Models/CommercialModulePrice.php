<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

/**
 * Price definition for a catalog module (billing model + optional tier ladder).
 */
class CommercialModulePrice extends Model
{
    use AuditTrait;

    protected $fillable = [
        'module_id',
        'billing_type',
        'base_price',
        'included_users',
        'price_per_user',
        'active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'included_users' => 'integer',
        'price_per_user' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function tiers(): HasMany
    {
        return $this->hasMany(CommercialModulePriceTier::class, 'module_price_id')->orderBy('from_users');
    }
}
