<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class CommercialPlan extends Model
{
    use AuditTrait;

    protected $fillable = [
        'code',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'active' => 'boolean',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(CommercialPlanTranslation::class);
    }

    public function commercialPlanModules(): HasMany
    {
        return $this->hasMany(CommercialPlanModule::class)->orderBy('sort_order');
    }

    public function commercialSubscriptions(): HasMany
    {
        return $this->hasMany(CommercialSubscription::class);
    }
}
