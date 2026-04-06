<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceDetailTopicCategoryTranslation extends Model
{
    use AuditTrait;

    protected $table = 'cat_service_detail_topic_category_translations';

    protected $fillable = [
        'service_detail_topic_category_id',
        'language_id',
        'name',
        'description',
    ];

    public function serviceDetailTopicCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceDetailTopicCategory::class, 'service_detail_topic_category_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
