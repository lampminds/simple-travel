<?php

namespace App\Filament\Resources\Authorization\PermissionResource\Pages;

use App\Filament\Resources\Authorization\PermissionResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditPermission extends LmpEditRecord
{
    protected static string $resource = PermissionResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
