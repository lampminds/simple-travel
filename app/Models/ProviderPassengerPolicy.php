<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ProviderPassengerPolicy extends Model
{
    use AuditTrait;

    protected $fillable = [
        'provider_id',
        'name',
    ];

    protected static function booted(): void
    {
        static::deleting(function (ProviderPassengerPolicy $policy): void {
            $ruleIds = $policy->rules()->pluck('id');

            if ($ruleIds->isNotEmpty()) {
                ServicePriceRule::query()
                    ->whereIn('provider_passenger_policy_rule_id', $ruleIds)
                    ->delete();
                $policy->rules()->delete();
            }

            Service::query()
                ->where('provider_passenger_policy_id', $policy->id)
                ->update(['provider_passenger_policy_id' => null]);
        });
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'provider_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'provider_passenger_policy_id');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(ProviderPassengerPolicyRule::class);
    }
}
