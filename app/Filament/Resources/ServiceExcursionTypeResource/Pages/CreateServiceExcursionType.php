<?php

namespace App\Filament\Resources\ServiceExcursionTypeResource\Pages;

use App\Filament\Resources\ServiceExcursionTypeResource;
use App\Models\Language;
use App\Models\ServiceExcursionType;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateServiceExcursionType extends LmpCreateRecord
{
    protected static string $resource = ServiceExcursionTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function fillForm(): void
    {
        parent::fillForm();
        $translations = [];
        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $translations[$lang->id] = ['name' => ''];
        }
        $state = $this->form->getRawState() ?? [];
        $state['translations'] = $translations;
        $this->form->fill($state);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return Arr::except($data, ['translations']);
    }

    protected function afterCreate(): void
    {
        $translations = $this->form->getState()['translations'] ?? [];
        $this->syncTranslations($this->getRecord(), $translations);
    }

    protected function syncTranslations(ServiceExcursionType $record, array $translations): void
    {
        foreach ($translations as $languageId => $row) {
            if (empty($row['name'] ?? '')) {
                continue;
            }
            $record->translations()->create([
                'language_id' => $languageId,
                'name' => $row['name'] ?? '',
            ]);
        }
    }
}
