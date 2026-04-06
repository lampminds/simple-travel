<?php

namespace App\Filament\Resources\PlanResource\Pages;

use App\Filament\Resources\PlanResource;
use App\Models\Language;
use App\Models\Plan;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreatePlan extends LmpCreateRecord
{
    protected static string $resource = PlanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function fillForm(): void
    {
        parent::fillForm();
        $translations = [];
        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $translations[$lang->id] = ['price' => null, 'name' => '', 'description' => null];
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

    protected function syncTranslations(Plan $record, array $translations): void
    {
        foreach ($translations as $languageId => $row) {
            $name = $row['name'] ?? '';
            $description = $row['description'] ?? null;
            $price = isset($row['price']) && $row['price'] !== '' && $row['price'] !== null ? $row['price'] : null;
            if ($name !== '' || $description !== null || $price !== null) {
                $record->translations()->create([
                    'language_id' => $languageId,
                    'price' => $price,
                    'name' => $name,
                    'description' => $description,
                ]);
            }
        }
    }
}
