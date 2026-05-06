<?php

namespace App\Filament\Resources\LmpStateResource\Pages;

use App\Filament\Resources\LmpStateResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditLmpState extends LmpEditRecord
{
    protected static string $resource = LmpStateResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
