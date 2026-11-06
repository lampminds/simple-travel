<?php

namespace App\Models;

use App\Enums\PriceAdjustmentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServicePriceRule extends Model
{
    use AuditTrait;

    protected $fillable = [
        'service_variant_id',
        'provider_price_list_item_id',
        'provider_passenger_policy_rule_id',
        'adjustment_type',
        'adjustment_value',
    ];

    protected $casts = [
        'adjustment_value' => 'decimal:2',
        'adjustment_type' => PriceAdjustmentType::class,
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class, 'service_variant_id');
    }

    public function priceListItem(): BelongsTo
    {
        return $this->belongsTo(PriceListItem::class, 'provider_price_list_item_id');
    }

    public function policyRule(): BelongsTo
    {
        return $this->belongsTo(ProviderPassengerPolicyRule::class, 'provider_passenger_policy_rule_id');
    }
}
