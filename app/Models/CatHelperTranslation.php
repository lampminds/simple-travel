<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class CatHelperTranslation extends Model
{
    use AuditTrait;

    protected $table = 'cat_helper_translations';

    protected $fillable = [
        'helper_id',
        'language_id',
        'text',
    ];

    public function helper(): BelongsTo
    {
        return $this->belongsTo(CatHelper::class, 'helper_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
