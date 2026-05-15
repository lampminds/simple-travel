<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceTransferLocationTypeCategoryTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_transfer_location_type_category_translations';

    protected $fillable = [
        'service_transfer_location_type_category_id',
        'language_id',
        'name',
    ];

    public function serviceTransferLocationTypeCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceTransferLocationTypeCategory::class, 'service_transfer_location_type_category_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
