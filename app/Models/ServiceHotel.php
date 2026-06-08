<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceHotel extends Model
{
    use AuditTrait;

    protected $table = 'service_hotels';

    protected $fillable = [
        'service_id',
        'stars',
        'check_in_time',
        'check_out_time',
    ];

    protected $casts = [
        'stars' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Catalogue hotel types assigned to this profile (many-to-many).
     */
    public function hotelTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            ServiceHotelType::class,
            'service_hotel_type_assignments',
            'service_hotel_id',
            'service_hotel_type_id'
        )->withTimestamps();
    }
}
