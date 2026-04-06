<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceFeatureCategoryTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_feature_category_translations';

    protected $fillable = [
        'service_feature_category_id',
        'language_id',
        'name',
    ];

    public function serviceFeatureCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceFeatureCategory::class, 'service_feature_category_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}

