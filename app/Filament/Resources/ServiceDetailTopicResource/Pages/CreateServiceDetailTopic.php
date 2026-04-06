<?php

namespace App\Filament\Resources\ServiceDetailTopicResource\Pages;

use App\Filament\Resources\ServiceDetailTopicResource;
use App\Models\Language;
use App\Models\ServiceDetailTopic;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateServiceDetailTopic extends LmpCreateRecord
{
    protected static string $resource = ServiceDetailTopicResource::class;

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

    protected function syncTranslations(ServiceDetailTopic $record, array $translations): void
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
