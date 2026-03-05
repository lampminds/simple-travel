<?php

namespace App\Filament\Resources\ContactTypeResource\Pages;

use App\Filament\Resources\ContactTypeResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpViewRecord;

class ViewContactType extends LmpViewRecord
{
    protected static string $resource = ContactTypeResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $record->load('translations');
        $data['translations'] = [];
        $languages = \App\Models\Language::query()->orderBy('id')->get();
        foreach ($languages as $lang) {
            $trans = $record->translations->firstWhere('language_id', $lang->id);
            $data['translations'][$lang->id] = $trans ? [
                'name' => $trans->name,
                'mask' => $trans->mask,
                'validation' => $trans->validation,
                'active' => $trans->active,
            ] : ['name' => '', 'mask' => null, 'validation' => null, 'active' => true];
        }

        return $data;
    }
}
