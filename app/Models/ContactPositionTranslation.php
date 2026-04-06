<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ContactPositionTranslation extends Model
{
    use AuditTrait;

    protected $table = 'cat_contact_position_translations';

    protected $fillable = [
        'contact_position_id',
        'language_id',
        'name',
    ];

    public function contactPosition(): BelongsTo
    {
        return $this->belongsTo(ContactPosition::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
