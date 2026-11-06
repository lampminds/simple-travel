<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class PassengerCategoryTranslation extends Model
{
    use AuditTrait;

    protected $fillable = [
        'passenger_category_id',
        'language_id',
        'name',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PassengerCategory::class, 'passenger_category_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
