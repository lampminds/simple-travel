<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServicePlanItemTranslation extends Model
{
    use AuditTrait;

    protected $fillable = [
        'service_plan_item_id',
        'language_id',
        'text',
    ];

    public function servicePlanItem(): BelongsTo
    {
        return $this->belongsTo(ServicePlanItem::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
