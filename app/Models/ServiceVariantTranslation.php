<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceVariantTranslation extends Model
{
    use AuditTrait;

    protected $table = 'service_variant_translations';

    protected $fillable = [
        'service_variant_id',
        'language_id',
        'name',
        'description',
    ];

    public function serviceVariant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
