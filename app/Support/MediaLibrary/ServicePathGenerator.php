<?php

namespace App\Support\MediaLibrary;

use App\Models\Service;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * Stores service media under accounts/{account_code}/services/{service_id}/...
 * Same relative layout works on local disks and S3 when using the service_media disk.
 */
class ServicePathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->basePath($media).'/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->basePath($media).'/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->basePath($media).'/responsive-images/';
    }

    protected function basePath(Media $media): string
    {
        $model = $media->model;
        if (! $model instanceof Service) {
            throw new \RuntimeException('ServicePathGenerator only supports media attached to '.Service::class);
        }

        $model->loadMissing('account');
        $code = $model->account?->code ?? 'unknown';
        $safeCode = preg_replace('/[^a-zA-Z0-9_-]/', '_', (string) $code);

        return 'accounts/'.$safeCode.'/services/'.$model->getKey().'/'.$media->getKey();
    }
}
