<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditUser extends LmpEditRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    /**
     * Load current user roles into the form.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['roles'] = $this->getRecord()->roles->pluck('id')->toArray();

        return $data;
    }

    /**
     * Persist roles relationship (Spatie) after the record is updated.
     */
    protected function afterSave(): void
    {
        $this->form->model($this->getRecord())->saveRelationships();
    }
}
