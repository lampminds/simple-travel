<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceActivityTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_activity_translations';

    protected $fillable = [
        'service_activity_id',
        'language_id',
        'name',
    ];

    public function serviceActivity(): BelongsTo
    {
        return $this->belongsTo(ServiceActivity::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
