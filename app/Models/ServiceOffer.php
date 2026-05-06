<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceOffer extends Model
{
    use AuditTrait;

    public const STATUS_PENDING = 'pending';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_REJECTED = 'rejected';

    public const AVAILABILITY_ACTIVE = 'active';

    public const AVAILABILITY_SUSPENDED = 'suspended';

    public const AVAILABILITY_DISCONTINUED = 'discontinued';

    protected $table = 'service_offers';

    protected $fillable = [
        'provider_id',
        'operator_id',
        'service_id',
        'service_variant_id',
        'status',
        'availability',
        'offered_at',
        'withdrawn_at',
    ];

    protected $casts = [
        'offered_at' => 'datetime',
        'withdrawn_at' => 'datetime',
    ];

    public function providerAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'provider_id');
    }

    public function operatorAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'operator_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function serviceVariant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class);
    }
}
