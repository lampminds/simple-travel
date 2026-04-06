<?php

namespace App\Filament\Resources\ServiceExcursionResource\Pages;

use App\Filament\Resources\ServiceExcursionResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditServiceExcursion extends LmpEditRecord
{
    protected static string $resource = ServiceExcursionResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
