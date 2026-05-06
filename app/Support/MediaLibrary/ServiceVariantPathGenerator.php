<?php

namespace App\Support\MediaLibrary;

use App\Models\ServiceVariant;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * Stores variant media under accounts/{account_code}/service-variants/{variant_id}/...
 * Uses the same service_media disk as {@see Service} uploads.
 */
class ServiceVariantPathGenerator implements PathGenerator
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
        if (! $model instanceof ServiceVariant) {
            throw new \RuntimeException('ServiceVariantPathGenerator only supports media attached to '.ServiceVariant::class);
        }

        $model->loadMissing('service.account');
        $code = $model->service?->account?->code ?? 'unknown';
        $safeCode = preg_replace('/[^a-zA-Z0-9_-]/', '_', (string) $code);

        return 'accounts/'.$safeCode.'/service-variants/'.$model->getKey().'/'.$media->getKey();
    }
}
