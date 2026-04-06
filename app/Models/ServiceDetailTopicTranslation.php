<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceDetailTopicTranslation extends Model
{
    use AuditTrait;

    protected $table = 'cat_service_detail_topic_translations';

    protected $fillable = [
        'service_detail_topic_id',
        'language_id',
        'name',
        'description',
    ];

    public function serviceDetailTopic(): BelongsTo
    {
        return $this->belongsTo(ServiceDetailTopic::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
