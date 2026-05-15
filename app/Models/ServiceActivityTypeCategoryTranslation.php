<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceActivityTypeCategoryTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_activity_type_category_translations';

    protected $fillable = [
        'service_activity_type_category_id',
        'language_id',
        'name',
    ];

    public function serviceActivityTypeCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceActivityTypeCategory::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
