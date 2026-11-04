<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Copies legacy local Media Library files onto the configured S3 disks.
 *
 * Source disks keep the previous local roots; destination disks use the same
 * names as Spatie and are backed by Vultr when MEDIA_STORAGE_DRIVER=s3.
 */
class MigrateLocalMediaToS3 extends Command
{
    protected $signature = 'storage:migrate-media-to-s3
                            {--disk=* : Logical disk names to migrate (default: all Spatie media disks)}
                            {--dry-run : List files without uploading}
                            {--delete-local : Delete each local file after a successful upload}
                            {--overwrite : Replace objects that already exist on S3}';

    protected $description = 'Migrate local Spatie media files to Vultr Object Storage (S3)';

    /**
     * @var array<string, string>
     */
    private array $sourceDiskByTarget = [
        'avatars' => 'avatars_local',
        'account_logos' => 'account_logos_local',
        'account_home_backgrounds' => 'account_home_backgrounds_local',
        'service_media' => 'service_media_local',
    ];

    public function handle(): int
    {
        if (env('MEDIA_STORAGE_DRIVER', 'local') !== 's3') {
            $this->error('MEDIA_STORAGE_DRIVER must be "s3" before migrating. Aborting.');

            return self::FAILURE;
        }

        $targets = $this->option('disk');
        if ($targets === [] || $targets === null) {
            $targets = array_keys($this->sourceDiskByTarget);
        }

        $dryRun = (bool) $this->option('dry-run');
        $deleteLocal = (bool) $this->option('delete-local');
        $overwrite = (bool) $this->option('overwrite');

        $uploaded = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($targets as $targetDisk) {
            if (! isset($this->sourceDiskByTarget[$targetDisk])) {
                $this->error('Unknown disk ['.$targetDisk.']. Allowed: '.implode(', ', array_keys($this->sourceDiskByTarget)));

                return self::FAILURE;
            }

            $sourceDisk = $this->sourceDiskByTarget[$targetDisk];
            $this->info("Migrating [{$sourceDisk}] → [{$targetDisk}]".($dryRun ? ' (dry-run)' : ''));

            try {
                $files = Storage::disk($sourceDisk)->allFiles();
            } catch (Throwable $e) {
                $this->warn("  Could not read source disk [{$sourceDisk}]: {$e->getMessage()}");

                continue;
            }

            if ($files === []) {
                $this->line('  No local files found.');

                continue;
            }

            foreach ($files as $path) {
                try {
                    if (! $overwrite && Storage::disk($targetDisk)->exists($path)) {
                        $this->line("  skip (exists): {$path}");
                        $skipped++;

                        continue;
                    }

                    if ($dryRun) {
                        $this->line("  would upload: {$path}");
                        $uploaded++;

                        continue;
                    }

                    $stream = Storage::disk($sourceDisk)->readStream($path);
                    if ($stream === false) {
                        throw new \RuntimeException('Could not open read stream.');
                    }

                    // Avoid ACL headers — Vultr buckets are private by policy.
                    $stored = Storage::disk($targetDisk)->put($path, $stream);

                    if (is_resource($stream)) {
                        fclose($stream);
                    }

                    if (! $stored) {
                        throw new \RuntimeException('S3 put() returned false.');
                    }

                    $this->line("  uploaded: {$path}");
                    $uploaded++;

                    if ($deleteLocal) {
                        Storage::disk($sourceDisk)->delete($path);
                        $this->line("  deleted local: {$path}");
                    }
                } catch (Throwable $e) {
                    $this->error("  failed: {$path} — {$e->getMessage()}");
                    $failed++;
                }
            }
        }

        $this->newLine();
        $this->info("Done. uploaded={$uploaded} skipped={$skipped} failed={$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
