<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class OperatorPackageConditionOverrideTranslation extends Model
{
    use AuditTrait;

    protected $fillable = [
        'operator_package_condition_override_id',
        'language_id',
        'custom_text',
    ];

    public function override(): BelongsTo
    {
        return $this->belongsTo(OperatorPackageConditionOverride::class, 'operator_package_condition_override_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
