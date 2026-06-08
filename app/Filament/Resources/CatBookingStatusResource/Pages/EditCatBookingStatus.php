<?php

namespace App\Filament\Resources\CatBookingStatusResource\Pages;

use App\Filament\Resources\CatBookingStatusResource;
use App\Models\CatBookingStatus;
use App\Models\Language;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditCatBookingStatus extends LmpEditRecord
{
    protected static string $resource = CatBookingStatusResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var CatBookingStatus $record */
        $record = $this->getRecord();
        $record->load('translations');
        $data['translations'] = [];

        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $trans = $record->translations->firstWhere('language_id', $lang->id);
            $data['translations'][$lang->id] = [
                'name' => $trans?->name ?? '',
                'help_tip' => $trans?->help_tip ?? '',
                'description' => $trans?->description ?? '',
            ];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        CatBookingStatusResource::assertUniqueCodeType($data, $this->getRecord()->getKey());

        return Arr::except($data, ['translations']);
    }

    protected function afterSave(): void
    {
        /** @var CatBookingStatus $record */
        $record = $this->getRecord();
        $translations = $this->form->getState()['translations'] ?? [];

        CatBookingStatusResource::syncTranslationsFromForm($record, $translations, false);
    }
}
