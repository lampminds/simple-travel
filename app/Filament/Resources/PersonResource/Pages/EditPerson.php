<?php

namespace App\Filament\Resources\PersonResource\Pages;

use App\Filament\Resources\PersonResource;
use Illuminate\Database\Eloquent\Model;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditPerson extends LmpEditRecord
{
    protected static string $resource = PersonResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    /**
     * Persist HasMany repeaters (user links, account rows, methods, cross-account links).
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record = parent::handleRecordUpdate($record, $data);
        $this->form->model($record)->saveRelationships();

        return $record;
    }
}
