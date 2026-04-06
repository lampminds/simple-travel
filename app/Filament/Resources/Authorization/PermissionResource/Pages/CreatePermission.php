<?php

namespace App\Filament\Resources\Authorization\PermissionResource\Pages;

use App\Filament\Resources\Authorization\PermissionResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreatePermission extends LmpCreateRecord
{
    protected static string $resource = PermissionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = parent::mutateFormDataBeforeCreate($data);
        $data['guard_name'] = $data['guard_name'] ?? 'web';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
