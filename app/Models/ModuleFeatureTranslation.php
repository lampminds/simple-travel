<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ModuleFeatureTranslation extends Model
{
    use AuditTrait;

    protected $fillable = [
        'module_feature_id',
        'language_id',
        'text',
    ];

    public function moduleFeature(): BelongsTo
    {
        return $this->belongsTo(ModuleFeature::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
