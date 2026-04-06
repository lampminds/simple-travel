<?php

namespace App\Filament\Resources\ServiceExcursionTypeResource\Pages;

use App\Filament\Resources\ServiceExcursionTypeResource;
use App\Models\Language;
use App\Models\ServiceExcursionType;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditServiceExcursionType extends LmpEditRecord
{
    protected static string $resource = ServiceExcursionTypeResource::class;

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
            ] : ['name' => ''];
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

    protected function syncTranslations(ServiceExcursionType $record, array $translations): void
    {
        $record->translations()->delete();
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
