<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

/**
 * Pivot: many catalogue activity types can be linked to one {@see ServiceActivity} profile.
 */
class ServiceActivityTypeAssignment extends Model
{
    use AuditTrait;

    protected $table = 'service_activity_type_assignments';

    protected $fillable = [
        'service_activity_id',
        'service_activity_type_id',
    ];

    public function serviceActivity(): BelongsTo
    {
        return $this->belongsTo(ServiceActivity::class);
    }

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(ServiceActivityType::class, 'service_activity_type_id');
    }
}
