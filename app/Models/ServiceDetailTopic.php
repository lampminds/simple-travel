<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceDetailTopic extends Model
{
    use AuditTrait;

    protected $table = 'cat_service_detail_topics';

    protected $fillable = [
        'code',
        'service_detail_topic_category_id',
        'visibility',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceDetailTopicCategory::class, 'service_detail_topic_category_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ServiceDetailTopicTranslation::class);
    }

    public function serviceDetails(): HasMany
    {
        return $this->hasMany(ServiceDetail::class);
    }

    public function getNameAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->name;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->description;
    }

    protected function getTranslationForDisplay(): ?ServiceDetailTopicTranslation
    {
        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
        }
        if ($this->translations->isEmpty()) {
            return null;
        }
        $locale = app()->getLocale();
        foreach ($this->translations as $translation) {
            $lang = $translation->language;
            if (! $lang) {
                continue;
            }
            $lang->loadMissing('locale');
            if (Locale::primaryTagMatches($lang->locale, $locale)) {
                return $translation;
            }
        }

        return $this->translations->first();
    }
}
