<?php

namespace App\Filament\Resources\ServiceExcursionResource\Pages;

use App\Filament\Resources\ServiceExcursionResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateServiceExcursion extends LmpCreateRecord
{
    protected static string $resource = ServiceExcursionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
