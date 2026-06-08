<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class CatFaqTranslation extends Model
{
    use AuditTrait;

    protected $table = 'cat_faq_translations';

    protected $fillable = [
        'faq_id',
        'language_id',
        'question',
        'answer',
    ];

    public function faq(): BelongsTo
    {
        return $this->belongsTo(CatFaq::class, 'faq_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
