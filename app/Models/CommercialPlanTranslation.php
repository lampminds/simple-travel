<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class CommercialPlanTranslation extends Model
{
    use AuditTrait;

    protected $fillable = [
        'commercial_plan_id',
        'language_id',
        'name',
        'description',
    ];

    public function commercialPlan(): BelongsTo
    {
        return $this->belongsTo(CommercialPlan::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
