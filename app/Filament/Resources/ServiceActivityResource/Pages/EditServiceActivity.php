<?php

namespace App\Filament\Resources\ServiceActivityResource\Pages;

use App\Filament\Resources\ServiceActivityResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditServiceActivity extends LmpEditRecord
{
    protected static string $resource = ServiceActivityResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
