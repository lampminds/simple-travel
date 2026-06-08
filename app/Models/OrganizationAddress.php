<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class OrganizationAddress extends Model
{
    use AuditTrait;

    protected $fillable = [
        'organization_id',
        'address_id',
        'type',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
