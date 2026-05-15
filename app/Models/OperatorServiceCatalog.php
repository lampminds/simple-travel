<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class OperatorServiceCatalog extends Model
{
    use AuditTrait;

    protected $table = 'operator_service_catalog';

    protected $fillable = [
        'operator_id',
        'provider_id',
        'service_id',
        'service_variant_id',
        'service_offer_id',
        'status',
        'is_featured',
        'is_public',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'operator_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'provider_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function serviceVariant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class);
    }

    public function serviceOffer(): BelongsTo
    {
        return $this->belongsTo(ServiceOffer::class);
    }
}
