<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceGastronomyCuisineTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_gastronomy_cuisine_translations';

    protected $fillable = [
        'service_gastronomy_cuisine_id',
        'language_id',
        'name',
    ];

    public function serviceGastronomyCuisine(): BelongsTo
    {
        return $this->belongsTo(ServiceGastronomyCuisine::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
