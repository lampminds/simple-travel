<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceVariantPaxBandPrice extends Model
{
    use AuditTrait;

    protected $fillable = [
        'service_variant_pax_band_id',
        'provider_passenger_policy_rule_id',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function band(): BelongsTo
    {
        return $this->belongsTo(ServiceVariantPaxBand::class, 'service_variant_pax_band_id');
    }

    public function policyRule(): BelongsTo
    {
        return $this->belongsTo(ProviderPassengerPolicyRule::class, 'provider_passenger_policy_rule_id');
    }
}
