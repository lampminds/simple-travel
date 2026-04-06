<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ContactTypeTranslation extends Model
{
    use AuditTrait;

    protected $table = 'cat_contact_type_translations';

    protected $fillable = [
        'contact_type_id',
        'language_id',
        'name',
        'mask',
        'validation',
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
