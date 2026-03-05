<?php

namespace App\Filament\Resources\ContactDepartmentResource\Pages;

use App\Filament\Resources\ContactDepartmentResource;
use App\Models\ContactDepartment;
use App\Models\Language;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateContactDepartment extends LmpCreateRecord
{
    protected static string $resource = ContactDepartmentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function fillForm(): void
    {
        parent::fillForm();
        $translations = [];
        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $translations[$lang->id] = ['code' => '', 'active' => true];
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

    protected function syncTranslations(ContactDepartment $record, array $translations): void
    {
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
