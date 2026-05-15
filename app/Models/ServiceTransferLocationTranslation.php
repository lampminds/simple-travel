<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceTransferLocationTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'service_transfer_location_translations';

    protected $fillable = [
        'service_transfer_location_id',
        'language_id',
        'name',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(ServiceTransferLocation::class, 'service_transfer_location_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
