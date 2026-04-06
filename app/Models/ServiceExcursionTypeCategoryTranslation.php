<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceExcursionTypeCategoryTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_excursion_type_category_translations';

    protected $fillable = [
        'service_excursion_type_category_id',
        'language_id',
        'name',
    ];

    public function serviceExcursionTypeCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceExcursionTypeCategory::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
