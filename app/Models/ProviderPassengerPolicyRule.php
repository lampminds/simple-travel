<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ProviderPassengerPolicyRule extends Model
{
    use AuditTrait;

    protected $fillable = [
        'provider_passenger_policy_id',
        'passenger_category_id',
        'age_min',
        'age_max',
    ];

    protected $casts = [
        'age_min' => 'integer',
        'age_max' => 'integer',
    ];

    public function policy(): BelongsTo
    {
        return $this->belongsTo(ProviderPassengerPolicy::class, 'provider_passenger_policy_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PassengerCategory::class, 'passenger_category_id');
    }

    public function ageRangeLabel(): string
    {
        if ($this->age_max === null) {
            return $this->age_min.'+';
        }

        return $this->age_min.'–'.$this->age_max;
    }
}
