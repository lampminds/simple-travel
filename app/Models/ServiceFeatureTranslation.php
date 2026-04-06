<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceFeatureTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_feature_translations';

    protected $fillable = [
        'service_feature_id',
        'language_id',
        'name',
        'description',
    ];

    public function serviceFeature(): BelongsTo
    {
        return $this->belongsTo(ServiceFeature::class, 'service_feature_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}

