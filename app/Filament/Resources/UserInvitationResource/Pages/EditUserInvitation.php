<?php

namespace App\Filament\Resources\UserInvitationResource\Pages;

use App\Filament\Resources\UserInvitationResource;
use App\Models\Role;
use App\Models\UserInvitation;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditUserInvitation extends LmpEditRecord
{
    protected static string $resource = UserInvitationResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['type'] ?? null) === UserInvitation::TYPE_EXTERNAL) {
            $data['role_id'] = Role::platformTemplateRoleIdOrFail('owner');
        }

        return $data;
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}

