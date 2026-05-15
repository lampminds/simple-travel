<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceTransferVehicleTypeCategoryTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_transfer_vehicle_type_category_translations';

    protected $fillable = [
        'service_transfer_vehicle_type_category_id',
        'language_id',
        'name',
    ];

    public function serviceTransferVehicleTypeCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceTransferVehicleTypeCategory::class, 'service_transfer_vehicle_type_category_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
