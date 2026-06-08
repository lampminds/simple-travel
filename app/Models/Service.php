<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as MediaModel;

class Service extends Model implements HasMedia
{
    use HasFactory, AuditTrait, HasUuid, InteractsWithMedia;

    public const MEDIA_COLLECTION_MAIN = 'main';

    public const MEDIA_COLLECTION_GALLERY = 'gallery';

    public const MEDIA_CONVERSION_THUMBNAIL = 'thumbnail';

    public const MEDIA_CONVERSION_SMALL = 'small';

    public const MEDIA_CONVERSION_REGULAR = 'regular';

    /**
     * Max upload size per image in kilobytes (Laravel `max` rule for files; Filament `maxSize`).
     * 3 MiB.
     */
    public const MEDIA_MAX_FILE_SIZE_KB = 3072;

    protected $fillable = [
        'account_id',
        'service_type_id',
        'city_id',
        'is_featured',
        'is_public',
        'booking_mode',
        'confirmation_time_hours',
        'status',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
        'booking_mode' => 'string',
        'confirmation_time_hours' => 'integer',
        'status' => 'string',
    ];

    /**
     * Get the account this service belongs to.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the service type.
     */
    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    /**
     * Get the city (on addons connection; no FK across DBs).
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(LmpCity::class, 'city_id');
    }

    /**
     * Get the catalogue experiences linked to this service (many-to-many via service_experience_assignments).
     */
    public function experiences(): BelongsToMany
    {
        return $this->belongsToMany(ServiceExperience::class, 'service_experience_assignments')
            ->withTimestamps();
    }

    /**
     * Get the detail records for this service.
     */
    public function serviceDetails(): HasMany
    {
        return $this->hasMany(ServiceDetail::class)->orderBy('sort_order');
    }

    /**
     * Activity / event service profile (1:1), when the service type is activity or event.
     */
    public function activity(): HasOne
    {
        return $this->hasOne(ServiceActivity::class);
    }

    /**
     * Gastronomy-specific profile (1:1), when the service type is gastronomy.
     */
    public function gastronomy(): HasOne
    {
        return $this->hasOne(ServiceGastronomy::class);
    }

    /**
     * Hotel / accommodation profile (1:1), when the service type is accommodation.
     */
    public function hotel(): HasOne
    {
        return $this->hasOne(ServiceHotel::class);
    }

    /**
     * Get the variants for this service.
     */
    public function serviceVariants(): HasMany
    {
        return $this->hasMany(ServiceVariant::class)->orderBy('sort_order');
    }

    /**
     * Get the translations for this service (one per language).
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ServiceTranslation::class);
    }

    /**
     * Catalogue features attached to this service (pivot: service_featurables).
     */
    public function features(): MorphToMany
    {
        return $this->morphToMany(
            ServiceFeature::class,
            'service_featurable',
            'service_featurables',
        )->withTimestamps();
    }

    /**
     * Get name for display (from translations).
     */
    public function getNameAttribute(): string
    {
        return $this->getTranslationForDisplay()?->name ?? '';
    }

    /**
     * Get description for display (from translations).
     */
    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->description;
    }

    /**
     * Translation to use for display (e.g. in tables and dropdowns).
     * Prefers the translation for the current app locale when available.
     */
    protected function getTranslationForDisplay(): ?ServiceTranslation
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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_MAIN)
            ->useDisk('service_media')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

        $this->addMediaCollection(self::MEDIA_COLLECTION_GALLERY)
            ->useDisk('service_media')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    /**
     * Max width conversions (height proportional); no upscaling (Fit::Max).
     */
    public function registerMediaConversions(?MediaModel $media = null): void
    {
        $this->addMediaConversion(self::MEDIA_CONVERSION_THUMBNAIL)
            ->fit(Fit::Max, 200, 10_000)
            ->performOnCollections(self::MEDIA_COLLECTION_MAIN, self::MEDIA_COLLECTION_GALLERY);

        $this->addMediaConversion(self::MEDIA_CONVERSION_SMALL)
            ->fit(Fit::Max, 800, 10_000)
            ->performOnCollections(self::MEDIA_COLLECTION_MAIN, self::MEDIA_COLLECTION_GALLERY);

        $this->addMediaConversion(self::MEDIA_CONVERSION_REGULAR)
            ->fit(Fit::Max, 1600, 10_000)
            ->performOnCollections(self::MEDIA_COLLECTION_MAIN, self::MEDIA_COLLECTION_GALLERY);
    }

    /**
     * Primary image URL for a given conversion, or null if none.
     */
    public function mainImageUrl(?string $conversion = null): ?string
    {
        $media = $this->getFirstMedia(self::MEDIA_COLLECTION_MAIN);
        if ($media === null) {
            return null;
        }

        if ($conversion !== null && $media->hasGeneratedConversion($conversion)) {
            return $media->getUrl($conversion);
        }

        return $media->getUrl();
    }

    /**
     * Gallery images ordered for slider (uses order_column).
     *
     * @return Collection<int, MediaModel>
     */
    public function galleryMedia(): Collection
    {
        return $this->getMedia(self::MEDIA_COLLECTION_GALLERY);
    }

    /**
     * Services that have at least one variant (required for catalog/commercial use).
     */
    public function scopeWithVariants(Builder $query): Builder
    {
        return $query->whereHas('serviceVariants');
    }

    public function hasVariants(): bool
    {
        if ($this->relationLoaded('serviceVariants')) {
            return $this->serviceVariants->isNotEmpty();
        }

        return $this->serviceVariants()->exists();
    }

    public function canBeActivated(): bool
    {
        return $this->hasVariants();
    }

    /**
     * Catalog states omitted from provider→operator offer pickers (given de baja).
     *
     * @return list<string>
     */
    public static function catalogStatusesOmittedFromOperatorOffers(): array
    {
        return ['discontinued', 'terminated'];
    }

    /**
     * Whether this service may appear in commercial operator-offer pickers.
     */
    public function catalogSelectableForOperatorOffers(): bool
    {
        return $this->status === 'active' && $this->hasVariants();
    }
}

