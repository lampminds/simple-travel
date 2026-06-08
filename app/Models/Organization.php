<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

/**
 * End-customer company managed by an agency account ({@see $agency_id}).
 */
class Organization extends Model
{
    use AuditTrait, HasUuid;

    protected $fillable = [
        'agency_id',
        'legal_name',
        'trade_name',
        'website',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'agency_id');
    }

    public function organizationAddresses(): HasMany
    {
        return $this->hasMany(OrganizationAddress::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(OrganizationDocument::class);
    }

    public function organizationPersons(): HasMany
    {
        return $this->hasMany(OrganizationPerson::class);
    }

    public function billingAddressLink(): HasOne
    {
        return $this->hasOne(OrganizationAddress::class)->where('type', 'billing');
    }

    public function displayName(): string
    {
        $trade = trim((string) $this->trade_name);

        return $trade !== '' ? $trade : trim((string) $this->legal_name);
    }
}
