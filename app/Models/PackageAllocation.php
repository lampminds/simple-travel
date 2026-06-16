<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class PackageAllocation extends Model
{
    use AuditTrait, HasUuid;

    public const TYPE_HARD = 'hard';

    public const TYPE_SOFT = 'soft';

    public const TYPE_FREE_SALE = 'free_sale';

    protected $table = 'package_allocations';

    protected $fillable = [
        'operator_service_catalog_id',
        'operator_id',
        'agency_id',
        'allocation_type',
        'capacity',
        'start_date',
        'end_date',
        'active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'active' => 'boolean',
    ];

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(OperatorServiceCatalog::class, 'operator_service_catalog_id');
    }

    public function operatorAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'operator_id');
    }

    public function agencyAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'agency_id');
    }
}
