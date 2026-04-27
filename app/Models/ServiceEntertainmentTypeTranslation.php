<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceEntertainmentTypeTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_entertainment_type_translations';

    protected $fillable = [
        'service_entertainment_type_id',
        'language_id',
        'name',
    ];

    public function serviceEntertainmentType(): BelongsTo
    {
        return $this->belongsTo(ServiceEntertainmentType::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}

