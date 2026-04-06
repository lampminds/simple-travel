<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class PlanUserPriceTranslation extends Model
{
    use AuditTrait;

    protected $table = 'plan_user_price_translations';

    protected $fillable = [
        'plan_user_price_id',
        'language_id',
        'price',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function planUserPrice(): BelongsTo
    {
        return $this->belongsTo(PlanUserPrice::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
