<?php

namespace App\Filament\Resources\ModuleResource\Pages;

use App\Filament\Resources\ModuleResource;
use App\Models\Language;
use App\Models\Module;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditModule extends LmpEditRecord
{
    protected static string $resource = ModuleResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $record->load('translations');
        $data['translations'] = [];
        $languages = Language::query()->orderBy('id')->get();
        foreach ($languages as $lang) {
            $trans = $record->translations->firstWhere('language_id', $lang->id);
            $data['translations'][$lang->id] = $trans ? [
                'name' => $trans->name ?? '',
                'description' => $trans->description,
            ] : ['name' => '', 'description' => null];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return Arr::except($data, ['translations', 'accountTypes']);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record = parent::handleRecordUpdate($record, $data);
        $this->form->model($record)->saveRelationships();

        return $record;
    }

    protected function afterSave(): void
    {
        /** @var Module $record */
        $record = $this->getRecord();
        $state = $this->form->getState();
        ModuleResource::syncTranslationsFromForm($record, $state['translations'] ?? []);
        ModuleResource::syncAllFeatureTranslationsFromFormState($record, $state['features'] ?? []);
    }
}
