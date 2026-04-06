<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceGastronomyVenueTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_gastronomy_venue_translations';

    protected $fillable = [
        'service_gastronomy_venue_id',
        'language_id',
        'name',
    ];

    public function serviceGastronomyVenue(): BelongsTo
    {
        return $this->belongsTo(ServiceGastronomyVenue::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
