<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishSiteAssets extends Command
{
    protected $signature = 'site:publish-assets {--force : Overwrite existing files}';

    protected $description = 'Copy tenant theme static files from resources/site/public/assets to public/{tenant_site.assets_path}';

    public function handle(): int
    {
        $source = resource_path('site/public/assets');
        $destDir = trim((string) config('tenant_site.assets_path', 'site/assets'), '/');
        $destination = public_path($destDir);

        if (! File::isDirectory($source)) {
            $this->error('Source not found: '.$source);

            return self::FAILURE;
        }

        if (File::isDirectory($destination) && ! $this->option('force')) {
            if (! $this->confirm('Destination already exists. Overwrite?', false)) {
                return self::SUCCESS;
            }
        }

        File::ensureDirectoryExists(dirname($destination));
        File::copyDirectory($source, $destination);

        $this->info('Published to: '.$destination);

        return self::SUCCESS;
    }
}
