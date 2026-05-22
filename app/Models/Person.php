<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as MediaModel;

class Person extends Model implements HasMedia
{
    use HasFactory, AuditTrait, InteractsWithMedia;

    /** Laravel would default to table `people` (irregular plural). */
    protected $table = 'persons';

    protected $fillable = [
        'name',
    ];

    /**
     * Account membership rows (department, position, flags per account).
     */
    public function accountPersons(): HasMany
    {
        return $this->hasMany(AccountPerson::class);
    }

    public function contactMethods(): HasMany
    {
        return $this->hasMany(PersonContactMethod::class);
    }

    public function contactLinks(): HasMany
    {
        return $this->hasMany(AccountContactLink::class);
    }

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'account_person')
            ->withPivot([
                'contact_department_id',
                'contact_position_id',
                'is_primary',
                'is_active',
                'is_public_contact',
                'is_preferred_contact_mode',
            ])
            ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_person')
            ->using(UserPerson::class)
            ->withTimestamps();
    }

    /**
     * Rows on user_person for Filament repeaters (same pivot as {@see users()}).
     */
    public function userPersons(): HasMany
    {
        return $this->hasMany(UserPerson::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->useDisk('avatars')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    /**
     * Max dimension conversions; preserve aspect ratio (no stretch).
     */
    public function registerMediaConversions(?MediaModel $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Max, 80, 80)
            ->nonQueued();

        $this->addMediaConversion('preview')
            ->fit(Fit::Max, 256, 256)
            ->nonQueued();
    }

    public function hasUploadedAvatar(): bool
    {
        return $this->getFirstMedia('avatar') !== null;
    }

    public function avatarThumbUrl(): string
    {
        $media = $this->getFirstMedia('avatar');
        if ($media !== null) {
            return $media->getUrl('thumb');
        }

        return $this->dicebearAvatarUrl(80);
    }

    public function avatarDisplayUrl(): string
    {
        $media = $this->getFirstMedia('avatar');
        if ($media !== null) {
            return $media->getUrl('preview');
        }

        return $this->dicebearAvatarUrl(256);
    }

    protected function dicebearAvatarUrl(int $size): string
    {
        $seed = substr(hash('sha256', 'person|'.$this->getKey().'|'.$this->name), 0, 32);

        return 'https://api.dicebear.com/9.x/avataaars/svg?'.http_build_query([
            'seed' => $seed,
            'size' => $size,
        ]);
    }
}
