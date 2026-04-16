<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditUser extends LmpEditRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    /**
     * Load memberships (account + role ids per Spatie team) into the form.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = parent::mutateFormDataBeforeFill($data);

        /** @var User $record */
        $record = $this->getRecord();

        $data['memberships'] = $record->accounts
            ->map(fn ($account): array => [
                'account_id' => $account->id,
                'role_ids' => $record->roleIdsForAccount((int) $account->id),
            ])
            ->values()
            ->all();

        return $data;
    }

    /**
     * Pivot / JSON keys must not be mass-assigned on the user row.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = parent::mutateFormDataBeforeSave($data);

        return Arr::except($data, ['memberships']);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $memberships = $this->form->getState()['memberships'] ?? [];

        $record = parent::handleRecordUpdate($record, $data);

        if ($record instanceof User) {
            $record->syncAccountMemberships($memberships);
        }

        return $record;
    }
}
