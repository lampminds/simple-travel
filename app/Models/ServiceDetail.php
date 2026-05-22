<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceDetail extends Model
{
    use AuditTrait;

    protected $table = 'service_details';

    protected $fillable = [
        'service_id',
        'service_detail_topic_id',
        'language_id',
        'description',
        'sort_order',
        'active',
        'is_mandatory',
        'condition_key_id',
    ];

    protected $casts = [
        'active' => 'boolean',
        'is_mandatory' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function serviceDetailTopic(): BelongsTo
    {
        return $this->belongsTo(ServiceDetailTopic::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function conditionKey(): BelongsTo
    {
        return $this->belongsTo(ServiceDetailConditionKey::class, 'condition_key_id');
    }
}
