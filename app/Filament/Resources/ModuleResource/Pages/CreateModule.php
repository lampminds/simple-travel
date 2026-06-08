<?php

namespace App\Filament\Resources\ModuleResource\Pages;

use App\Filament\Resources\ModuleResource;
use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateModule extends LmpCreateRecord
{
    protected static string $resource = ModuleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function fillForm(): void
    {
        parent::fillForm();
        $translations = [];
        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $translations[$lang->id] = ['name' => '', 'description' => null];
        }
        $state = $this->form->getRawState() ?? [];
        $state['translations'] = $translations;
        $this->form->fill($state);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return Arr::except($data, ['translations', 'accountTypes']);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = parent::handleRecordCreation($data);
        $this->form->model($record)->saveRelationships();

        return $record;
    }

    protected function afterCreate(): void
    {
        $state = $this->form->getState();
        ModuleResource::syncTranslationsFromForm($this->getRecord(), $state['translations'] ?? []);
    }
}
