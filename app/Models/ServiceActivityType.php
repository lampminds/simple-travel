<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceActivityType extends Model
{
    use HasFactory, AuditTrait;

    protected $table = 'cat_service_activity_types';

    protected $fillable = [
        'code',
        'service_activity_type_category_id',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceActivityTypeCategory::class, 'service_activity_type_category_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ServiceActivityTypeTranslation::class);
    }

    /**
     * @return BelongsToMany<ServiceActivity, ServiceActivityType>
     */
    public function serviceActivities(): BelongsToMany
    {
        return $this->belongsToMany(
            ServiceActivity::class,
            'service_activity_type_assignments',
            'service_activity_type_id',
            'service_activity_id'
        )->withTimestamps();
    }

    public function getNameAttribute(): string
    {
        return $this->getTranslationForDisplay()?->name ?? '';
    }

    protected function getTranslationForDisplay(): ?ServiceActivityTypeTranslation
    {
        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $translations = $this->translations;

        if ($translations->isEmpty()) {
            return null;
        }

        $locale = app()->getLocale();
        foreach ($translations as $translation) {
            $lang = $translation->language;
            if (! $lang) {
                continue;
            }
            $lang->loadMissing('locale');
            if (Locale::primaryTagMatches($lang->locale, $locale)) {
                return $translation;
            }
        }

        return $translations->first();
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
