<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Resources\ContactResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditContact extends LmpEditRecord
{
    protected static string $resource = ContactResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
