<?php

namespace App\Filament\Resources\UserInvitationResource\Pages;

use App\Filament\Resources\UserInvitationResource;
use App\Models\Role;
use App\Models\UserInvitation;
use Illuminate\Support\Str;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateUserInvitation extends LmpCreateRecord
{
    protected static string $resource = UserInvitationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = $data['status'] ?? UserInvitation::STATUS_PENDING;
        $data['token'] = ($data['token'] ?? '') !== '' ? $data['token'] : Str::random(64);
        if (($data['type'] ?? null) === UserInvitation::TYPE_EXTERNAL) {
            $data['role_id'] = $data['role_id'] ?? Role::platformTemplateRoleIdOrFail('owner');
        }
        if (empty($data['account_inviting']) && ! empty($data['account_id'])) {
            $data['account_inviting'] = (int) $data['account_id'];
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}

