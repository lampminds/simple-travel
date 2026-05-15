<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

/**
 * Pivot: which catalog modules are bundled into a commercial plan (display order per plan).
 */
class CommercialPlanModule extends Model
{
    use AuditTrait;

    protected $fillable = [
        'commercial_plan_id',
        'module_id',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function commercialPlan(): BelongsTo
    {
        return $this->belongsTo(CommercialPlan::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
