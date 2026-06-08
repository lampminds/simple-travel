<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class CatGenderTranslation extends Model
{
    use AuditTrait;

    protected $table = 'cat_gender_translations';

    protected $fillable = [
        'gender_id',
        'language_id',
        'name',
    ];

    public function gender(): BelongsTo
    {
        return $this->belongsTo(CatGender::class, 'gender_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
