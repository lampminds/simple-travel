<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceExperienceCategoryTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_experience_category_translations';

    protected $fillable = [
        'service_experience_category_id',
        'language_id',
        'name',
    ];

    public function serviceExperienceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceExperienceCategory::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
