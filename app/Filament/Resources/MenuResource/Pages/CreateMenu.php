<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use App\Models\Language;
use App\Models\Menu;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateMenu extends LmpCreateRecord
{
    protected static string $resource = MenuResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function fillForm(): void
    {
        parent::fillForm();

        $translations = [];
        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $translations[$lang->id] = ['name' => '', 'tip' => ''];
        }

        $state = $this->form->getRawState() ?? [];
        $state['translations'] = $translations;

        $duplicateId = request()->integer('duplicate');
        if ($duplicateId > 0) {
            $source = Menu::query()->find($duplicateId);
            if ($source !== null) {
                $state = array_merge($state, MenuResource::duplicateFormDefaults($source));
            }
        }

        $parentId = request()->integer('parent_id');
        if ($parentId > 0) {
            $state['parent_id'] = $parentId;
        }

        $this->form->fill($state);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = Arr::except($data, ['translations', 'accountTypes']);

        if (isset($data['parent_id']) && $data['parent_id'] === '') {
            $data['parent_id'] = null;
        }

        if (! isset($data['sort_order']) || $data['sort_order'] === '' || $data['sort_order'] === null) {
            $pid = $data['parent_id'] ?? null;
            if ($pid === '') {
                $pid = null;
            }
            $data['sort_order'] = (int) Menu::query()
                ->where('parent_id', $pid)
                ->max('sort_order') + 1;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->form->model($this->getRecord())->saveRelationships();

        $state = $this->form->getState();
        MenuResource::syncMenuTranslations($this->getRecord(), $state['translations'] ?? []);
    }
}
