<?php

namespace App\Filament\Resources\Authorization\RoleResource\Pages;

use App\Filament\Resources\Authorization\RoleResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateRole extends LmpCreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = parent::mutateFormDataBeforeCreate($data);
        $data['account_id'] = (int) config('permission.platform_account_id', 1);
        $data['guard_name'] = $data['guard_name'] ?? 'web';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
