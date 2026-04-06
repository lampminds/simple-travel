<?php

namespace App\Filament\Resources\ServiceGastronomyTypeResource\Pages;

use App\Filament\Resources\ServiceGastronomyTypeResource;
use App\Models\Language;
use App\Models\ServiceGastronomyType;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditServiceGastronomyType extends LmpEditRecord
{
    protected static string $resource = ServiceGastronomyTypeResource::class;

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
                'name' => $trans->name,
                'description' => $trans->description,
            ] : ['name' => '', 'description' => ''];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return Arr::except($data, ['translations']);
    }

    protected function afterSave(): void
    {
        $this->syncTranslations($this->getRecord(), $this->form->getState()['translations'] ?? []);
    }

    protected function syncTranslations(ServiceGastronomyType $record, array $translations): void
    {
        $record->translations()->delete();
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
