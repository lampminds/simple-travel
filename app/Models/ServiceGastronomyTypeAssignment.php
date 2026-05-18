<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

/**
 * Pivot: many catalogue gastronomy types can be linked to one {@see ServiceGastronomy} profile.
 */
class ServiceGastronomyTypeAssignment extends Model
{
    use AuditTrait;

    protected $table = 'service_gastronomy_type_assignments';

    protected $fillable = [
        'service_gastronomy_id',
        'service_gastronomy_type_id',
    ];

    public function serviceGastronomy(): BelongsTo
    {
        return $this->belongsTo(ServiceGastronomy::class);
    }

    public function gastronomyType(): BelongsTo
    {
        return $this->belongsTo(ServiceGastronomyType::class, 'service_gastronomy_type_id');
    }
}
