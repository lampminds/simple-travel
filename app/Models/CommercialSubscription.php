<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class CommercialSubscription extends Model
{
    use AuditTrait, HasUuid;

    protected $fillable = [
        'account_id',
        'commercial_plan_id',
        'starts_at',
        'ends_at',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function commercialPlan(): BelongsTo
    {
        return $this->belongsTo(CommercialPlan::class);
    }

    public function commercialSubscriptionModules(): HasMany
    {
        return $this->hasMany(CommercialSubscriptionModule::class)->orderBy('sort_order');
    }
}
