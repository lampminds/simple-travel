<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceTransferLocationTypeTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'service_transfer_location_type_translations';

    protected $fillable = [
        'service_transfer_location_type_id',
        'language_id',
        'name',
    ];

    public function locationType(): BelongsTo
    {
        return $this->belongsTo(ServiceTransferLocationType::class, 'service_transfer_location_type_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
