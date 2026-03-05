<?php

namespace App\Filament\Resources\ContactDepartmentResource\Pages;

use App\Filament\Resources\ContactDepartmentResource;
use App\Models\ContactDepartment;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditContactDepartment extends LmpEditRecord
{
    protected static string $resource = ContactDepartmentResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $record->load('translations');
        $data['translations'] = [];
        $languages = \App\Models\Language::query()->orderBy('id')->get();
        foreach ($languages as $lang) {
            $trans = $record->translations->firstWhere('language_id', $lang->id);
            $data['translations'][$lang->id] = $trans ? [
                'code' => $trans->code,
                'active' => $trans->active,
            ] : ['code' => '', 'active' => true];
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

    protected function syncTranslations(ContactDepartment $record, array $translations): void
    {
        $record->translations()->delete();
        foreach ($translations as $languageId => $row) {
            if (empty($row['code'])) {
                continue;
            }
            $record->translations()->create([
                'language_id' => $languageId,
                'code' => $row['code'] ?? '',
                'active' => $row['active'] ?? true,
            ]);
        }
    }
}
