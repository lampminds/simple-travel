<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class PlanItemTranslation extends Model
{
    use AuditTrait;

    protected $table = 'plan_feature_translations';

    protected $fillable = [
        'plan_feature_id',
        'language_id',
        'text',
    ];

    public function planItem(): BelongsTo
    {
        return $this->belongsTo(PlanItem::class, 'plan_feature_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
