<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ProviderBookingTemplateItemTranslation extends Model
{
    use AuditTrait;

    protected $fillable = [
        'provider_booking_template_item_id',
        'language_id',
        'label',
        'help_text',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(ProviderBookingTemplateItem::class, 'provider_booking_template_item_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
