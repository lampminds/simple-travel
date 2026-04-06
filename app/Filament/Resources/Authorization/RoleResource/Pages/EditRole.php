<?php

namespace App\Filament\Resources\Authorization\RoleResource\Pages;

use App\Filament\Resources\Authorization\RoleResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditRole extends LmpEditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
