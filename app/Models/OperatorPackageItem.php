<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class OperatorPackageItem extends Model
{
    use AuditTrait;

    protected $table = 'operator_package_items';

    protected $fillable = [
        'operator_service_catalog_id',
        'service_id',
        'service_variant_id',
        'service_offer_id',
        'day_number',
        'sort_order',
        'quantity',
        'inclusion_mode',
        'notes',
    ];

    protected $casts = [
        'day_number' => 'integer',
        'sort_order' => 'integer',
        'quantity' => 'integer',
    ];

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(OperatorServiceCatalog::class, 'operator_service_catalog_id');
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
