<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ProviderBookingFieldTranslation extends Model
{
    use AuditTrait;

    protected $fillable = [
        'provider_booking_field_id',
        'language_id',
        'label',
        'help_text',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(ProviderBookingField::class, 'provider_booking_field_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
