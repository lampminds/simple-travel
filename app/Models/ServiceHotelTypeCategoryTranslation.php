<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceHotelTypeCategoryTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_hotel_type_category_translations';

    protected $fillable = [
        'service_hotel_type_category_id',
        'language_id',
        'name',
    ];

    public function serviceHotelTypeCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceHotelTypeCategory::class, 'service_hotel_type_category_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
