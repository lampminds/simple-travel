<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class PlanTranslation extends Model
{
    use AuditTrait;

    protected $table = 'plan_translations';

    protected $fillable = [
        'plan_id',
        'language_id',
        'price',
        'name',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
