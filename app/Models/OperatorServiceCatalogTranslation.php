<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class OperatorServiceCatalogTranslation extends Model
{
    use AuditTrait;

    protected $fillable = [
        'operator_service_catalog_id',
        'language_id',
        'name',
        'slug',
        'description',
    ];

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(OperatorServiceCatalog::class, 'operator_service_catalog_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
