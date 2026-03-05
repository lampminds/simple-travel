<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditAccount extends LmpEditRecord
{
    protected static string $resource = AccountResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
