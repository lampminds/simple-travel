<?php

namespace App\Filament\Resources\ContactDepartmentResource\Pages;

use App\Filament\Resources\ContactDepartmentResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpViewRecord;

class ViewContactDepartment extends LmpViewRecord
{
    protected static string $resource = ContactDepartmentResource::class;

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
            ] : ['name' => ''];
        }

        return $data;
    }
}
