<?php

namespace App\Filament\Resources\ServiceHotelTypeResource\Pages;

use App\Filament\Resources\ServiceHotelTypeResource;
use App\Models\Language;
use App\Models\ServiceHotelType;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateServiceHotelType extends LmpCreateRecord
{
    protected static string $resource = ServiceHotelTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function fillForm(): void
    {
        parent::fillForm();
        $translations = [];
        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $translations[$lang->id] = ['name' => '', 'description' => ''];
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

    protected function syncTranslations(ServiceHotelType $record, array $translations): void
    {
        foreach ($translations as $languageId => $row) {
            if (empty($row['name'] ?? '') && empty($row['description'] ?? '')) {
                continue;
            }
            $record->translations()->create([
                'language_id' => $languageId,
                'name' => $row['name'] ?? '',
                'description' => $row['description'] ?? null,
            ]);
        }
    }
}
