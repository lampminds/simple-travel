<?php

namespace App\Support\MediaLibrary;

use DateTimeInterface;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;
use Throwable;

/**
 * Makes Media::getUrl() usable with private S3 disks by returning signed URLs.
 */
class TemporaryUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        if (! $this->canUseTemporaryUrls()) {
            return parent::getUrl();
        }

        try {
            // Do not wrap with versionUrl(): appending ?v= breaks S3 signatures.
            return $this->getTemporaryUrl($this->displayExpiration());
        } catch (Throwable $exception) {
            report($exception);

            return parent::getUrl();
        }
    }

    protected function canUseTemporaryUrls(): bool
    {
        if (! $this->getDisk()->providesTemporaryUrls()) {
            return false;
        }

        $bucket = $this->getDisk()->getConfig()['bucket'] ?? null;

        return filled($bucket);
    }

    protected function displayExpiration(): DateTimeInterface
    {
        return now()->addMinutes((int) config('filesystems.signed_url_ttl_public', 1440));
    }
}
