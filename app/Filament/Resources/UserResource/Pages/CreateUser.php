<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;
use Spatie\Permission\PermissionRegistrar;

class CreateUser extends LmpCreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    /**
     * Attach accounts and roles; Spatie team must be set before role sync.
     */
    protected function afterCreate(): void
    {
        $user = $this->getRecord()->fresh(['accounts']);
        $accountId = $user->accounts()->orderBy('accounts.id')->value('accounts.id');
        if ($accountId) {
            app(PermissionRegistrar::class)->setPermissionsTeamId((int) $accountId);
        }
        $this->form->model($user)->saveRelationships();
    }
}
