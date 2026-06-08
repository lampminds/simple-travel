<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class Allocation extends Model
{
    use AuditTrait, HasUuid;

    public const TYPE_HARD = 'hard';

    public const TYPE_SOFT = 'soft';

    public const TYPE_FREE_SALE = 'free_sale';

    protected $table = 'allocations';

    protected $fillable = [
        'service_variant_id',
        'provider_id',
        'operator_id',
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

    public function serviceVariant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class);
    }

    public function service(): HasOneThrough
    {
        return $this->hasOneThrough(
            Service::class,
            ServiceVariant::class,
            'id',
            'id',
            'service_variant_id',
            'service_id',
        );
    }

    public function providerAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'provider_id');
    }

    public function operatorAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'operator_id');
    }
}
