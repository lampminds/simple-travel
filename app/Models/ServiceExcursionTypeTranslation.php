<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceExcursionTypeTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_excursion_type_translations';

    protected $fillable = [
        'service_excursion_type_id',
        'language_id',
        'name',
    ];

    public function serviceExcursionType(): BelongsTo
    {
        return $this->belongsTo(ServiceExcursionType::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
