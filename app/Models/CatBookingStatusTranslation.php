<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class CatBookingStatusTranslation extends Model
{
    use AuditTrait;

    protected $table = 'cat_booking_status_translations';

    protected $fillable = [
        'status_id',
        'language_id',
        'name',
        'help_tip',
        'description',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(CatBookingStatus::class, 'status_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
