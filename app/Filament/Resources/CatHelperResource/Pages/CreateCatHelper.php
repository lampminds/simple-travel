<?php

namespace App\Filament\Resources\CatHelperResource\Pages;

use App\Filament\Resources\CatHelperResource;
use App\Models\CatHelper;
use App\Models\Language;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateCatHelper extends LmpCreateRecord
{
    protected static string $resource = CatHelperResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function fillForm(): void
    {
        parent::fillForm();

        $translations = [];
        foreach (Language::query()->with('locale')->orderBy('id')->get() as $lang) {
            $translations[$lang->id] = [
                'text' => '',
            ];
        }
        $state = $this->form->getRawState() ?? [];
        $state['translations'] = $translations;

        $duplicateId = request()->integer('duplicate');
        if ($duplicateId > 0) {
            $source = CatHelper::query()->find($duplicateId);
            if ($source !== null) {
                $state = array_merge($state, CatHelperResource::duplicateFormDefaults($source));
            }
        }

        $this->form->fill($state);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = CatHelperResource::normalizeScopeFields($data);
        CatHelperResource::assertUniqueScope($data, null);

        return Arr::except($data, ['translations']);
    }

    protected function afterCreate(): void
    {
        /** @var CatHelper $record */
        $record = $this->getRecord();
        $translations = $this->form->getState()['translations'] ?? [];

        CatHelperResource::syncTranslationsFromForm($record, $translations, true);
    }
}
