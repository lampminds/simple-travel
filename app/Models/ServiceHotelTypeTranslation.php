<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceHotelTypeTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_hotel_type_translations';

    protected $fillable = [
        'service_hotel_type_id',
        'language_id',
        'name',
        'description',
    ];

    public function serviceHotelType(): BelongsTo
    {
        return $this->belongsTo(ServiceHotelType::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
