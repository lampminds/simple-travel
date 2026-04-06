<?php

namespace App\Filament\Resources\ServiceDetailTopicCategoryResource\Pages;

use App\Filament\Resources\ServiceDetailTopicCategoryResource;
use App\Models\Language;
use App\Models\ServiceDetailTopicCategory;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateServiceDetailTopicCategory extends LmpCreateRecord
{
    protected static string $resource = ServiceDetailTopicCategoryResource::class;

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
        return Arr::except($data, ['translations']);
    }

    protected function afterCreate(): void
    {
        $this->syncTranslations($this->getRecord(), $this->form->getState()['translations'] ?? []);
    }

    protected function syncTranslations(ServiceDetailTopicCategory $record, array $translations): void
    {
        foreach ($translations as $languageId => $row) {
            $name = $row['name'] ?? '';
            $description = $row['description'] ?? null;
            if ($name !== '' || $description !== null) {
                $record->translations()->create([
                    'language_id' => $languageId,
                    'name' => $name,
                    'description' => $description,
                ]);
            }
        }
    }
}
