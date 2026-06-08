<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as MediaModel;

class ServiceVariant extends Model implements HasMedia
{
    use AuditTrait, HasUuid, InteractsWithMedia;

    public const MEDIA_COLLECTION_MAIN = 'main';

    public const MEDIA_COLLECTION_GALLERY = 'gallery';

    public const MEDIA_CONVERSION_THUMBNAIL = 'thumbnail';

    public const MEDIA_CONVERSION_SMALL = 'small';

    public const MEDIA_CONVERSION_REGULAR = 'regular';

    /** @see Service::MEDIA_MAX_FILE_SIZE_KB */
    public const MEDIA_MAX_FILE_SIZE_KB = 3072;

    protected $table = 'service_variants';

    /** Columns on service_variants (see migration; no rules/overrides/slots here). */
    protected $fillable = [
        'service_id',
        'sku',
        'status',
        'pricing_type',
        'base_price',
        'currency_id',
        'inventory_type',
        'inventory_total',
        'capacity_min',
        'capacity_max',
        'min_advance_booking_hours',
        'max_advance_booking_days',
        'start_time',
        'end_time',
        'sort_order',
    ];

    protected $casts = [
        'capacity_min' => 'integer',
        'capacity_max' => 'integer',
        'base_price' => 'decimal:2',
        'inventory_total' => 'integer',
        'min_advance_booking_hours' => 'integer',
        'max_advance_booking_days' => 'integer',
        'sort_order' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Transfer profile for this variant (1:1), when the parent service type is transfer.
     */
    public function transfer(): HasOne
    {
        return $this->hasOne(ServiceTransfer::class);
    }

    /**
     * Get the currency for this variant (cat_currencies).
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Localized name and description (one row per language).
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ServiceVariantTranslation::class);
    }

    /**
     * Display name from translations (prefers current app locale).
     */
    public function getNameAttribute(): string
    {
        return $this->getTranslationForDisplay()?->name ?? '';
    }

    /**
     * Translation row for display (wizard list, dropdowns).
     */
    protected function getTranslationForDisplay(): ?ServiceVariantTranslation
    {
        if (! $this->relationLoaded('translations')) {
            $this->load('translations.language.locale');
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
     * @return Collection<int, MediaModel>
     */
    public function galleryMedia(): Collection
    {
        return $this->getMedia(self::MEDIA_COLLECTION_GALLERY);
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
     * Whether this variant may be toggled for operator proposals (active service + active variant).
     */
    public function catalogSelectableForOperatorOffers(?Service $parentService = null): bool
    {
        $service = $parentService ?? $this->service;

        return $this->status === 'active'
            && $service !== null
            && $service->status === 'active';
    }
}
