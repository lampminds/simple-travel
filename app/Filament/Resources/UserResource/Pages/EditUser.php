<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
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
     * BelongsToMany keys must not be mass-assigned on the user row.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = parent::mutateFormDataBeforeSave($data);

        return Arr::except($data, ['accounts', 'roles']);
    }

    /**
     * Filament EditRecord does not call saveRelationships(); create flow does.
     * Run it here so Select::saveRelationshipsUsing runs for accounts + roles (Spatie team order).
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record = parent::handleRecordUpdate($record, $data);
        $this->form->model($record)->saveRelationships();

        return $record;
    }
}
