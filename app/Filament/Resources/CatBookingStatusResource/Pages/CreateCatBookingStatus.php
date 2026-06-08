<?php

namespace App\Filament\Resources\CatBookingStatusResource\Pages;

use App\Filament\Resources\CatBookingStatusResource;
use App\Models\CatBookingStatus;
use App\Models\Language;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateCatBookingStatus extends LmpCreateRecord
{
    protected static string $resource = CatBookingStatusResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function fillForm(): void
    {
        parent::fillForm();

        $translations = [];
        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $translations[$lang->id] = ['name' => '', 'help_tip' => '', 'description' => ''];
        }
        $state = $this->form->getRawState() ?? [];
        $state['translations'] = $translations;
        $this->form->fill($state);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        CatBookingStatusResource::assertUniqueCodeType($data, null);

        return Arr::except($data, ['translations']);
    }

    protected function afterCreate(): void
    {
        /** @var CatBookingStatus $record */
        $record = $this->getRecord();
        $translations = $this->form->getState()['translations'] ?? [];

        CatBookingStatusResource::syncTranslationsFromForm($record, $translations, true);
    }
}
