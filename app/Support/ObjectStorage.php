<?php

namespace App\Support;

use DateTimeInterface;
use Illuminate\Support\Facades\Storage;

/**
 * Helpers for private S3-compatible buckets (Vultr Object Storage).
 *
 * Plain Storage::url() points at the object path but is not readable when the
 * bucket is private. These helpers always return signed URLs.
 */
class ObjectStorage
{
    /**
     * Signed URL for displayable assets (avatars, logos, banners).
     */
    public static function url(string $path, ?string $disk = null, ?DateTimeInterface $expiration = null): string
    {
        $diskName = $disk ?? (string) config('filesystems.default');
        $expiration ??= now()->addMinutes((int) config('filesystems.signed_url_ttl_public', 1440));

        return Storage::disk($diskName)->temporaryUrl($path, $expiration);
    }

    /**
     * Short-lived signed URL for protected / download-only files.
     */
    public static function temporaryUrl(string $path, ?string $disk = null, ?DateTimeInterface $expiration = null): string
    {
        $diskName = $disk ?? (string) config('filesystems.default');
        $expiration ??= now()->addMinutes((int) config('filesystems.signed_url_ttl_protected', 15));

        return Storage::disk($diskName)->temporaryUrl($path, $expiration);
    }
}
