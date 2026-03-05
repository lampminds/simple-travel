<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServicePlanTranslation extends Model
{
    use AuditTrait;

    protected $fillable = [
        'service_plan_id',
        'language_id',
        'price',
        'name',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function servicePlan(): BelongsTo
    {
        return $this->belongsTo(ServicePlan::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
