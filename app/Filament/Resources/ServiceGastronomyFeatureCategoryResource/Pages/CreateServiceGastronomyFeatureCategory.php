<?php

namespace App\Filament\Resources\ServiceGastronomyFeatureCategoryResource\Pages;

use App\Filament\Resources\ServiceGastronomyFeatureCategoryResource;
use App\Models\Language;
use App\Models\ServiceGastronomyFeatureCategory;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateServiceGastronomyFeatureCategory extends LmpCreateRecord
{
    protected static string $resource = ServiceGastronomyFeatureCategoryResource::class;

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

    /**
     * @param array<int|string, array{name:string}> $translations
     */
    protected function syncTranslations(ServiceGastronomyFeatureCategory $record, array $translations): void
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
