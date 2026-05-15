<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

/**
 * Pivot: module enablement and optional negotiated price for a concrete subscription.
 */
class CommercialSubscriptionModule extends Model
{
    use AuditTrait;

    protected $fillable = [
        'commercial_subscription_id',
        'module_id',
        'enabled',
        'custom_price',
        'sort_order',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'custom_price' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function commercialSubscription(): BelongsTo
    {
        return $this->belongsTo(CommercialSubscription::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
