<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceDetailTopicCategory extends Model
{
    use AuditTrait;

    protected $table = 'cat_service_detail_topic_categories';

    protected $fillable = [
        'code',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(ServiceDetailTopicCategoryTranslation::class, 'service_detail_topic_category_id');
    }

    public function serviceDetailTopics(): HasMany
    {
        return $this->hasMany(ServiceDetailTopic::class, 'service_detail_topic_category_id');
    }

    public function getNameAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->name;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->description;
    }

    protected function getTranslationForDisplay(): ?ServiceDetailTopicCategoryTranslation
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
