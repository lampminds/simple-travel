<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateUser extends LmpCreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function fillForm(): void
    {
        parent::fillForm();
        $auth = auth()->user();
        if ($auth instanceof User && ! $auth->belongsToPlatformAccount()) {
            $accountId = $auth->currentAccountId();
            if ($accountId !== null) {
                $state = $this->form->getRawState() ?? [];
                $state['accounts'] = [$accountId];
                $this->form->fill($state);
            }
        }
    }

    /**
     * BelongsToMany fields are persisted in {@see UserResource} Select::saveRelationshipsUsing}
     * when Filament runs saveRelationships() after the user row exists.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = parent::mutateFormDataBeforeCreate($data);

        return Arr::except($data, ['accounts', 'roles']);
    }
}
