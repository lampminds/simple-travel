<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceExperienceTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_experience_translations';

    protected $fillable = [
        'service_experience_id',
        'language_id',
        'name',
    ];

    public function serviceExperience(): BelongsTo
    {
        return $this->belongsTo(ServiceExperience::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
