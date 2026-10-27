<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as MediaModel;

class Account extends Model implements HasMedia
{
    use HasFactory, AuditTrait, HasUuid, InteractsWithMedia;

    public const MEDIA_COLLECTION_LOGO = 'logo';

    /** Mark meant to sit on a white surface, such as the sign-in card. */
    public const MEDIA_COLLECTION_LOGO_ON_WHITE = 'logo_on_white';

    public const MEDIA_CONVERSION_LOGO_PREVIEW = 'preview';

    public const MEDIA_MAX_LOGO_KB = 2048;

    /**
     * Raster logos plus SVG, so the mark can stay sharp and transparent.
     *
     * @var list<string>
     */
    public const LOGO_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
        'image/svg+xml',
    ];

    public const MEDIA_COLLECTION_HOME_BACKGROUND = 'home_background';

    public const MEDIA_CONVERSION_HOME_BACKGROUND = 'cover';

    public const MEDIA_MAX_HOME_BACKGROUND_KB = 5120;

    /**
     * Hero images. The check uses the detected type, not the filename:
     * an export can keep a .jpeg name while the bytes are AVIF.
     *
     * @var list<string>
     */
    public const HOME_BACKGROUND_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/avif',
    ];

    public const ADDRESS_TYPE_PRIMARY = 'primary';

    protected $fillable = [
        'nick',
        'code',
        'name',
        'commercial_name',
        'portal_host',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Address links for this account. The edit forms maintain {@see self::ADDRESS_TYPE_PRIMARY}.
     */
    public function accountAddresses(): HasMany
    {
        return $this->hasMany(AccountAddress::class);
    }

    public function primaryAddressLink(): HasOne
    {
        return $this->hasOne(AccountAddress::class)->where('type', self::ADDRESS_TYPE_PRIMARY);
    }

    public function primaryAddress(): ?Address
    {
        $link = $this->relationLoaded('primaryAddressLink')
            ? $this->primaryAddressLink
            : $this->primaryAddressLink()->with('address')->first();

        return $link?->address;
    }

    /**
     * Create or update the single primary address. An empty payload removes it.
     *
     * @param  array{address_line1?: string|null, address_line2?: string|null, city_id?: int|string|null, postal_code?: string|null}  $input
     */
    public function syncPrimaryAddress(array $input): void
    {
        $line1 = trim((string) ($input['address_line1'] ?? ''));
        $line2 = trim((string) ($input['address_line2'] ?? ''));
        $postal = trim((string) ($input['postal_code'] ?? ''));
        $cityId = is_numeric($input['city_id'] ?? null) ? (int) $input['city_id'] : null;
        if ($cityId !== null && $cityId < 1) {
            $cityId = null;
        }

        $link = $this->primaryAddressLink()->with('address')->first();

        if ($line1 === '' && $line2 === '' && $postal === '' && $cityId === null) {
            $this->deletePrimaryAddress($link);

            return;
        }

        $payload = [
            'address_line_1' => $line1,
            'address_line_2' => $line2 !== '' ? $line2 : null,
            'postal_code' => $postal !== '' ? $postal : null,
            'city_id' => null,
            'city' => null,
            'state_id' => null,
            'state' => null,
            'country_id' => null,
        ];

        if ($cityId !== null) {
            $city = LmpCity::query()->with(['state.country'])->find($cityId);
            if ($city instanceof LmpCity) {
                $payload['city_id'] = (int) $city->id;
                $payload['city'] = $city->name;
                $payload['state_id'] = $city->state_id;
                $payload['state'] = $city->state?->name;
                $payload['country_id'] = $city->state?->country_id;
            }
        }

        if ($link?->address instanceof Address) {
            $link->address->update($payload);

            return;
        }

        $address = Address::query()->create($payload);
        $this->accountAddresses()->create([
            'address_id' => $address->id,
            'type' => self::ADDRESS_TYPE_PRIMARY,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{address_line1: mixed, address_line2: mixed, city_id: mixed, postal_code: mixed}
     */
    public static function pullAddressInput(array &$data): array
    {
        $input = [
            'address_line1' => $data['address_line1'] ?? null,
            'address_line2' => $data['address_line2'] ?? null,
            'city_id' => $data['city_id'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
        ];

        unset(
            $data['address_line1'],
            $data['address_line2'],
            $data['city_id'],
            $data['postal_code'],
            $data['state_name'],
            $data['country_name'],
        );

        return $input;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function mergePrimaryAddressIntoFormData(array $data): array
    {
        $address = $this->primaryAddress();
        $data['address_line1'] = $address?->address_line_1;
        $data['address_line2'] = $address?->address_line_2;
        $data['city_id'] = $address?->city_id;
        $data['postal_code'] = $address?->postal_code;

        return $data;
    }

    private function deletePrimaryAddress(?AccountAddress $link): void
    {
        if (! $link instanceof AccountAddress) {
            return;
        }

        $address = $link->address;
        $link->delete();

        if (! $address instanceof Address) {
            return;
        }

        $stillLinked = AccountAddress::query()->where('address_id', $address->id)->exists()
            || OrganizationAddress::query()->where('address_id', $address->id)->exists();

        if (! $stillLinked) {
            $address->delete();
        }
    }

    /**
     * Business types assigned to this account ({@see AccountType}, pivot account_type_assignments).
     */
    public function accountTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            AccountType::class,
            'account_type_assignments',
            'account_id',
            'account_type_id'
        );
    }

    /**
     * Documents stored for this account (e.g. tax IDs, group tax_id in cat_documents).
     */
    public function documents(): HasMany
    {
        return $this->hasMany(AccountDocument::class);
    }

    /**
     * Contact channels stored on this account (email, phone, WhatsApp, …).
     */
    public function contactMethods(): HasMany
    {
        return $this->hasMany(AccountContactMethod::class);
    }

    public function publicProfile(): HasOne
    {
        return $this->hasOne(AccountPublicProfile::class);
    }

    public function hasActiveType(string $code): bool
    {
        $types = $this->relationLoaded('accountTypes')
            ? $this->accountTypes
            : $this->accountTypes()->get();

        return $types->contains(
            fn (AccountType $type): bool => $type->code === $code && (bool) $type->active
        );
    }

    /**
     * Company clients (organizations) owned by this agency account.
     */
    public function clientOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'agency_id');
    }

    /**
     * Individual person clients managed by this account (account_person with link_type client).
     */
    public function clientPersons(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'account_person')
            ->wherePivot('link_type', AccountPerson::LINK_CLIENT)
            ->withPivot([
                'link_type',
                'is_active',
            ])
            ->withTimestamps();
    }

    /**
     * Users that belong to this account (account_user pivot).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Notifications shared at account level (read once for all account members).
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(AccountNotification::class);
    }

    /**
     * Commercial SaaS subscriptions purchased by this account.
     */
    public function commercialSubscriptions(): HasMany
    {
        return $this->hasMany(CommercialSubscription::class);
    }

    /**
     * Persons linked to this account with department/position (account_person).
     */
    public function accountPersons(): HasMany
    {
        return $this->hasMany(AccountPerson::class);
    }

    public function persons(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'account_person')
            ->withPivot([
                'link_type',
                'contact_department_id',
                'contact_position_id',
                'is_primary',
                'is_active',
                'is_public_contact',
                'is_preferred_contact_mode',
            ])
            ->withTimestamps();
    }

    /**
     * Staff / member persons linked to this account (excludes client links).
     */
    public function memberPersons(): BelongsToMany
    {
        return $this->persons()->wherePivot('link_type', AccountPerson::LINK_MEMBER);
    }

    /**
     * Contact links this account stores (person from another account).
     */
    public function accountContactLinks(): HasMany
    {
        return $this->hasMany(AccountContactLink::class);
    }

    /**
     * How many of provider, operator, and agency (active business types) are assigned to this account.
     */
    public function businessDashboardLaneCount(): int
    {
        return $this->businessDashboardLaneInfo()['count'];
    }

    /**
     * @return array{count: int, hasProvider: bool, hasOperator: bool, hasAgency: bool}
     */
    protected function businessDashboardLaneInfo(): array
    {
        $typeCodes = $this->accountTypes()
            ->where((new AccountType)->getTable().'.active', true)
            ->pluck((new AccountType)->getTable().'.code');

        $hasProvider = $typeCodes->contains('provider');
        $hasOperator = $typeCodes->contains('operator');
        $hasAgency = $typeCodes->contains('agency');

        return [
            'count' => (int) $hasProvider + (int) $hasOperator + (int) $hasAgency,
            'hasProvider' => $hasProvider,
            'hasOperator' => $hasOperator,
            'hasAgency' => $hasAgency,
        ];
    }

    /**
     * When this account maps to exactly one frontend dashboard lane (provider, operator, or agency), return that route name.
     * Operator accounts map to the operator dashboard.
     */
    public function soleDashboardRouteName(): ?string
    {
        $i = $this->businessDashboardLaneInfo();

        if ($i['count'] !== 1) {
            return null;
        }

        if ($i['hasProvider']) {
            return 'provider.dashboard';
        }

        if ($i['hasOperator']) {
            return 'operator.dashboard';
        }

        return 'agency.dashboard';
    }

    public function registerMediaCollections(): void
    {
        foreach ([self::MEDIA_COLLECTION_LOGO, self::MEDIA_COLLECTION_LOGO_ON_WHITE] as $collection) {
            $this->addMediaCollection($collection)
                ->useDisk('account_logos')
                ->singleFile()
                ->acceptsMimeTypes(self::LOGO_MIME_TYPES);
        }

        $this->addMediaCollection(self::MEDIA_COLLECTION_HOME_BACKGROUND)
            ->useDisk('account_home_backgrounds')
            ->singleFile()
            ->acceptsMimeTypes(self::HOME_BACKGROUND_MIME_TYPES);
    }

    /**
     * Header-sized preview. Not queued, so the portal can use it right after upload.
     */
    public function registerMediaConversions(?MediaModel $media = null): void
    {
        // SVG stays a vector. A raster preview would flatten transparency and the original size.
        if (! self::mediaIsSvg($media)) {
            $this->addMediaConversion(self::MEDIA_CONVERSION_LOGO_PREVIEW)
                ->fit(Fit::Max, 480, 160)
                ->nonQueued()
                ->performOnCollections(
                    self::MEDIA_COLLECTION_LOGO,
                    self::MEDIA_COLLECTION_LOGO_ON_WHITE,
                );
        }

        // AVIF is stored as uploaded. GD may not decode it, and a failed
        // conversion would reject the whole save.
        if (! self::mediaIsAvif($media)) {
            $this->addMediaConversion(self::MEDIA_CONVERSION_HOME_BACKGROUND)
                ->fit(Fit::Max, 1920, 1080)
                ->nonQueued()
                ->performOnCollections(self::MEDIA_COLLECTION_HOME_BACKGROUND);
        }
    }

    /**
     * Storage name for a home background.
     *
     * The extension follows the detected MIME type. The public disk sends
     * Content-Type from the extension, so AVIF bytes named .jpeg would not render.
     */
    public static function homeBackgroundFileName(UploadedFile $file): string
    {
        $extension = match ($file->getMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/avif' => 'avif',
            default => strtolower($file->getClientOriginalExtension()) ?: 'jpg',
        };

        return Str::ulid().'.'.$extension;
    }

    /**
     * Public URL of a logo, or null when the account has none.
     * Defaults to the mark used over photographs.
     */
    public function portalLogoUrl(?string $collection = null): ?string
    {
        $media = $this->logoMedia($collection);
        if ($media === null) {
            return null;
        }

        if ($media->hasGeneratedConversion(self::MEDIA_CONVERSION_LOGO_PREVIEW)) {
            return $media->getUrl(self::MEDIA_CONVERSION_LOGO_PREVIEW);
        }

        return $media->getUrl();
    }

    /**
     * Root-relative logo path, so the portal host serves the file itself.
     * Defaults to the mark used over photographs (site header).
     */
    public function portalLogoPath(?string $collection = null): ?string
    {
        $media = $this->logoMedia($collection);
        if ($media === null) {
            return null;
        }

        $conversion = $media->hasGeneratedConversion(self::MEDIA_CONVERSION_LOGO_PREVIEW)
            ? self::MEDIA_CONVERSION_LOGO_PREVIEW
            : '';

        return '/storage/account-logos/'.ltrim($media->getPathRelativeToRoot($conversion), '/');
    }

    /**
     * Logo for white surfaces, such as the sign-in card. Null when none was uploaded.
     */
    public function portalLogoOnWhitePath(): ?string
    {
        return $this->portalLogoPath(self::MEDIA_COLLECTION_LOGO_ON_WHITE);
    }

    private function logoMedia(?string $collection = null): ?MediaModel
    {
        return $this->getFirstMedia($collection ?? self::MEDIA_COLLECTION_LOGO);
    }

    /**
     * Root-relative home hero image, or null when the account has none.
     */
    public function homeBackgroundUrl(): ?string
    {
        $media = $this->getFirstMedia(self::MEDIA_COLLECTION_HOME_BACKGROUND);
        if ($media === null) {
            return null;
        }

        $conversion = $media->hasGeneratedConversion(self::MEDIA_CONVERSION_HOME_BACKGROUND)
            ? self::MEDIA_CONVERSION_HOME_BACKGROUND
            : '';

        return '/storage/account-home-backgrounds/'.ltrim($media->getPathRelativeToRoot($conversion), '/');
    }

    /**
     * True when this media file is a vector logo and must not be rasterized.
     */
    public static function mediaIsSvg(?MediaModel $media): bool
    {
        if (! $media instanceof MediaModel) {
            return false;
        }

        return $media->mime_type === 'image/svg+xml'
            || strtolower((string) $media->extension) === 'svg';
    }

    /**
     * True when this media file is AVIF and must not be passed through GD.
     */
    public static function mediaIsAvif(?MediaModel $media): bool
    {
        if (! $media instanceof MediaModel) {
            return false;
        }

        return $media->mime_type === 'image/avif'
            || strtolower((string) $media->extension) === 'avif';
    }

    /**
     * City and country of the primary address, for the public site header.
     */
    public function publicLocationLabel(): ?string
    {
        $address = $this->primaryAddress();
        if (! $address instanceof Address) {
            return null;
        }

        $city = trim((string) $address->city);
        if ($city === '' && $address->city_id) {
            $city = trim((string) (LmpCity::query()->find($address->city_id)?->name ?? ''));
        }

        $country = '';
        if ($address->country_id) {
            $country = trim((string) (LmpCountry::query()->find($address->country_id)?->name ?? ''));
        }

        $parts = array_values(array_filter([$city, $country], fn (string $part): bool => $part !== ''));

        return $parts === [] ? null : implode(', ', $parts);
    }

    /**
     * Primary email contact method, or the first email when none is marked primary.
     */
    public function publicEmail(): ?string
    {
        $query = $this->contactMethods()
            ->whereHas('contactType', fn ($types) => $types->where('code', 'email'));

        $email = (clone $query)->where('is_primary', true)->value('value')
            ?? $query->value('value');
        $email = trim((string) $email);

        return $email !== '' ? $email : null;
    }

    /**
     * Hostname only: lowercase, no scheme, port, or path.
     */
    public static function normalizePortalHost(?string $value): ?string
    {
        $value = strtolower(trim((string) $value));
        if ($value === '') {
            return null;
        }

        if (str_contains($value, '://')) {
            $host = parse_url($value, PHP_URL_HOST);
            $value = is_string($host) ? strtolower($host) : $value;
        }

        $value = explode('/', $value, 2)[0];
        if (str_contains($value, ':')) {
            $value = explode(':', $value, 2)[0];
        }

        $value = rtrim($value, '.');

        return $value !== '' ? $value : null;
    }

    public function setPortalHostAttribute(?string $value): void
    {
        $this->attributes['portal_host'] = static::normalizePortalHost($value);
    }

    public static function isValidPortalHost(string $host): bool
    {
        return (bool) preg_match(
            '/^(?=.{1,255}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/',
            $host
        );
    }

    /**
     * Significant characters allowed in the alias portion of an account code.
     * Hyphens and the 3-digit numeric suffix do not count toward this limit.
     */
    public const CODE_BASE_MAX_SIGNIFICANT_LENGTH = 15;

    /**
     * Generate a unique code in the form [Alias]-NNN,
     * where NNN is 3 random digits from 2-9 (no zeros nor ones).
     * The alias keeps at most 15 characters, ignoring hyphens.
     */
    public static function generateCode(string $nick): string
    {
        $base = static::codeBaseFromNick($nick);
        $digits = '23456789';

        do {
            $nnn = '';
            for ($i = 0; $i < 3; $i++) {
                $nnn .= $digits[random_int(0, 7)];
            }
            $code = $base . '-' . $nnn;
        } while (static::where('code', $code)->exists());

        return $code;
    }

    /**
     * Alias portion of an account code, taken from the nick.
     * Hyphens are kept as separators and do not count toward the 15-character limit.
     */
    public static function codeBaseFromNick(string $nick): string
    {
        $base = '';
        $significant = 0;
        $length = mb_strlen($nick);

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($nick, $i, 1);

            if ($char === '-') {
                if ($base !== '' && ! str_ends_with($base, '-')) {
                    $base .= '-';
                }

                continue;
            }

            if ($significant >= self::CODE_BASE_MAX_SIGNIFICANT_LENGTH) {
                break;
            }

            $base .= $char;
            $significant++;
        }

        $base = rtrim($base, '-');

        return $base !== '' ? $base : 'account';
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($account) {
            if (empty($account->code) && ! empty($account->nick)) {
                $account->code = static::generateCode($account->nick);
            }
        });
    }
}
