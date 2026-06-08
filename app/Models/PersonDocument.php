<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class PersonDocument extends Model
{
    use AuditTrait;

    protected $fillable = [
        'person_id',
        'document_id',
        'value',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(CatDocument::class, 'document_id');
    }
}
