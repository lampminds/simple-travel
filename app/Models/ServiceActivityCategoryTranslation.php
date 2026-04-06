<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceActivityCategoryTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_activity_category_translations';

    protected $fillable = [
        'service_activity_category_id',
        'language_id',
        'name',
    ];

    public function serviceActivityCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceActivityCategory::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
