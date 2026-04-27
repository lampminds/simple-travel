<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceEntertainmentTypeCategoryTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_entertainment_type_category_translations';

    protected $fillable = [
        'service_entertainment_type_category_id',
        'language_id',
        'name',
    ];

    public function serviceEntertainmentTypeCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceEntertainmentTypeCategory::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}

