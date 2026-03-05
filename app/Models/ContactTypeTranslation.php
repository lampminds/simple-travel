<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ContactTypeTranslation extends Model
{
    use AuditTrait;

    protected $fillable = [
        'contact_type_id',
        'language_id',
        'name',
        'mask',
        'validation',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function contactType(): BelongsTo
    {
        return $this->belongsTo(ContactType::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
