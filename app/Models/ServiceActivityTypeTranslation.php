<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceActivityTypeTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_activity_type_translations';

    protected $fillable = [
        'service_activity_type_id',
        'language_id',
        'name',
    ];

    public function serviceActivityType(): BelongsTo
    {
        return $this->belongsTo(ServiceActivityType::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
