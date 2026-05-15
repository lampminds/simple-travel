<?php

namespace App\Filament\Resources\ServiceActivityResource\Pages;

use App\Filament\Resources\ServiceActivityResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateServiceActivity extends LmpCreateRecord
{
    protected static string $resource = ServiceActivityResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
