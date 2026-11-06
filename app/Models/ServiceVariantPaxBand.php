<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceVariantPaxBand extends Model
{
    use AuditTrait;

    protected $fillable = [
        'service_variant_id',
        'min_pax',
        'max_pax',
        'price',
        'sort_order',
    ];

    protected $casts = [
        'min_pax' => 'integer',
        'max_pax' => 'integer',
        'price' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class, 'service_variant_id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ServiceVariantPaxBandPrice::class);
    }

    public function covers(int $partySize): bool
    {
        if ($partySize < $this->min_pax) {
            return false;
        }

        return $this->max_pax === null || $partySize <= $this->max_pax;
    }

    /**
     * Per-person amount for one age band. A missing cell uses the regular price.
     */
    public function amountForPolicyRule(?int $policyRuleId): float
    {
        if ($policyRuleId !== null) {
            $cell = $this->prices->first(
                fn (ServiceVariantPaxBandPrice $price): bool => (int) $price->provider_passenger_policy_rule_id === $policyRuleId
            );

            if ($cell !== null) {
                return (float) $cell->price;
            }
        }

        return (float) $this->price;
    }

    public function rangeLabel(): string
    {
        if ($this->max_pax === null) {
            return $this->min_pax.'+';
        }

        if ($this->min_pax === $this->max_pax) {
            return (string) $this->min_pax;
        }

        return $this->min_pax.'–'.$this->max_pax;
    }
}
