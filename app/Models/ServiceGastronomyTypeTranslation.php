<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceGastronomyTypeTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_gastronomy_type_translations';

    protected $fillable = [
        'service_gastronomy_type_id',
        'language_id',
        'name',
        'description',
    ];

    public function serviceGastronomyType(): BelongsTo
    {
        return $this->belongsTo(ServiceGastronomyType::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
