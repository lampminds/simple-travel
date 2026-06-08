<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class CatDocumentTranslation extends Model
{
    use AuditTrait;

    protected $table = 'cat_document_translations';

    protected $fillable = [
        'document_id',
        'language_id',
        'name',
        'description',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(CatDocument::class, 'document_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
