<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class PackageOffer extends Model
{
    use AuditTrait, HasUuid;

    public const STATUS_PENDING = 'pending';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_REJECTED = 'rejected';

    public const AVAILABILITY_ACTIVE = 'active';

    public const AVAILABILITY_SUSPENDED = 'suspended';

    public const AVAILABILITY_DISCONTINUED = 'discontinued';

    protected $table = 'package_offers';

    protected $fillable = [
        'operator_id',
        'agency_id',
        'operator_service_catalog_id',
        'operator_price_list_id',
        'status',
        'availability',
        'offered_at',
        'withdrawn_at',
    ];

    protected $casts = [
        'offered_at' => 'datetime',
        'withdrawn_at' => 'datetime',
    ];

    public function operatorAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'operator_id');
    }

    public function agencyAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'agency_id');
    }

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(OperatorServiceCatalog::class, 'operator_service_catalog_id');
    }

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(OperatorPriceList::class, 'operator_price_list_id');
    }
}
