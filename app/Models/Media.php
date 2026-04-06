<?php

namespace App\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    public function getTable(): string
    {
        return (string) config('media-library.table_name', 'sys_media');
    }
}
