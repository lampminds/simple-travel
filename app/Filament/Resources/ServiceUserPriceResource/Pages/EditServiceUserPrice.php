<?php

namespace App\Filament\Resources\ServiceUserPriceResource\Pages;

use App\Models\Language;
use App\Models\ServiceUserPrice;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditServiceUserPrice extends LmpEditRecord
{
    protected static string $resource = \App\Filament\Resources\ServiceUserPriceResource::class;

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
                'price' => $trans->price,
                'description' => $trans->description,
            ] : ['price' => null, 'description' => null];
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

    protected function syncTranslations(ServiceUserPrice $record, array $translations): void
    {
        $record->translations()->delete();
        foreach ($translations as $languageId => $row) {
            $description = $row['description'] ?? null;
            $price = isset($row['price']) && $row['price'] !== '' && $row['price'] !== null ? $row['price'] : null;
            if ($description !== null || $price !== null) {
                $record->translations()->create([
                    'language_id' => $languageId,
                    'price' => $price,
                    'description' => $description,
                ]);
            }
        }
    }
}
