<?php

/**
 * Build a Spatie media disk that is local by default, or Vultr S3 when
 * MEDIA_STORAGE_DRIVER=s3 (production). Disk names stay stable so the media
 * table does not need updates on cutover.
 *
 * @return array<string, mixed>
 */
$mediaDisk = static function (string $directory): array {
    if (env('MEDIA_STORAGE_DRIVER', 'local') === 's3') {
        return [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'atl2'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'root' => $directory,
            // Avoid public-read ACL; Vultr buckets are private by policy.
            'visibility' => 'private',
            'throw' => env('AWS_THROW_ON_ERROR', false),
            'report' => false,
        ];
    }

    return [
        'driver' => 'local',
        'root' => storage_path('app/public/'.$directory),
        'url' => rtrim((string) env('APP_URL', 'http://localhost'), '/').'/storage/'.$directory,
        'visibility' => 'public',
        'throw' => false,
        'report' => false,
    ];
};

/**
 * Fixed local root used only by storage:migrate-media-to-s3 as the copy source.
 *
 * @return array<string, mixed>
 */
$localMigrationDisk = static function (string $directory): array {
    return [
        'driver' => 'local',
        'root' => storage_path('app/public/'.$directory),
        'visibility' => 'public',
        'throw' => false,
        'report' => false,
    ];
};

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Signed URL lifetimes (minutes) for private object storage
    |--------------------------------------------------------------------------
    |
    | Private Vultr buckets cannot be served with plain Storage::url(). Spatie
    | TemporaryUrlGenerator and App\Support\ObjectStorage use these TTLs.
    |
    */

    'signed_url_ttl_public' => (int) env('STORAGE_SIGNED_URL_TTL_PUBLIC', 60 * 24),

    'signed_url_ttl_protected' => (int) env('STORAGE_SIGNED_URL_TTL_PROTECTED', 15),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim((string) env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        /*
         * User profile avatars only (Spatie Media Library collection "avatar").
         */
        'avatars' => $mediaDisk('avatars'),

        /*
         * Account logos (Spatie collections "logo" and "logo_on_white").
         */
        'account_logos' => $mediaDisk('account-logos'),

        /*
         * Optional home-page hero images (Spatie collection "home_background" on accounts).
         */
        'account_home_backgrounds' => $mediaDisk('account-home-backgrounds'),

        /*
         * Service images (Spatie Media Library). Paths from ServicePathGenerator
         * stay the same under this disk root.
         */
        'service_media' => $mediaDisk('service-media'),

        /*
         * visibility=private avoids Laravel's default public-read ACL on S3 puts.
         * Access is still via signed URLs; objects are not world-readable.
         */
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'atl2'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'visibility' => 'private',
            'throw' => env('AWS_THROW_ON_ERROR', false),
            'report' => false,
        ],

        /*
         * Legacy local roots used only by storage:migrate-media-to-s3.
         */
        'avatars_local' => $localMigrationDisk('avatars'),
        'account_logos_local' => $localMigrationDisk('account-logos'),
        'account_home_backgrounds_local' => $localMigrationDisk('account-home-backgrounds'),
        'service_media_local' => $localMigrationDisk('service-media'),

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
