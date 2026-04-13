<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use App\Models\Language;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditMenu extends LmpEditRecord
{
    protected static string $resource = MenuResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Menu $record */
        $record = $this->getRecord();
        $record->load('translations');
        $data['translations'] = [];
        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $trans = $record->translations->firstWhere('language_id', $lang->id);
            $data['translations'][$lang->id] = [
                'name' => $trans?->name ?? '',
                'tip' => $trans?->tip ?? '',
            ];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var Menu $record */
        $record = $this->getRecord();

        $data = Arr::except($data, ['translations', 'accountTypes']);

        if (isset($data['parent_id']) && $data['parent_id'] === '') {
            $data['parent_id'] = null;
        }

        $parentId = isset($data['parent_id']) ? (int) $data['parent_id'] : null;
        if ($parentId !== null && $parentId < 1) {
            $data['parent_id'] = null;
            $parentId = null;
        }

        if ($record->wouldCreateParentCycle($parentId)) {
            throw ValidationException::withMessages([
                'parent_id' => __('filament.resources.menu_validation.parent_cycle'),
            ]);
        }

        return $data;
    }

    /**
     * Persist BelongsToMany (account types) after the main row update.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record = parent::handleRecordUpdate($record, $data);
        $this->form->model($record)->saveRelationships();

        return $record;
    }

    protected function afterSave(): void
    {
        $state = $this->form->getState();
        MenuResource::syncMenuTranslations($this->getRecord(), $state['translations'] ?? []);
    }
}
