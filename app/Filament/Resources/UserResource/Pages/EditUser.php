<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;
use Spatie\Permission\PermissionRegistrar;

class EditUser extends LmpEditRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    /**
     * Load current user roles and account memberships into the form.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $teamId = $record->accounts()->orderBy('accounts.id')->value('accounts.id');
        if ($teamId !== null) {
            app(PermissionRegistrar::class)->setPermissionsTeamId((int) $teamId);
        }
        $record->unsetRelation('roles');
        $data['roles'] = $record->roles->pluck('id')->toArray();
        $data['accounts'] = $record->accounts->pluck('id')->toArray();

        return $data;
    }

    /**
     * Persist accounts and roles; Spatie roles need the team (account) set before sync.
     */
    protected function afterSave(): void
    {
        $user = $this->getRecord()->fresh(['accounts']);
        $accountId = $user->accounts()->orderBy('accounts.id')->value('accounts.id');
        if ($accountId) {
            app(PermissionRegistrar::class)->setPermissionsTeamId((int) $accountId);
        }
        $this->form->model($user)->saveRelationships();
    }
}
