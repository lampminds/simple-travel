<?php

namespace App\Filament\Resources\PlanItemResource\Pages;

use App\Filament\Resources\PlanItemResource;
use App\Filament\Resources\PlanResource;
use App\Models\Language;
use App\Models\PlanItem;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditPlanItem extends LmpEditRecord
{
    protected static string $resource = PlanItemResource::class;

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
            $data['translations'][$lang->id] = ['text' => $trans?->text ?? ''];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = Arr::except($data, ['translations']);

        if (isset($data['parent_id']) && $data['parent_id'] === '') {
            $data['parent_id'] = null;
        }

        /** @var PlanItem $record */
        $record = $this->getRecord();
        if (isset($data['parent_id']) && (int) $data['parent_id'] === (int) $record->id) {
            $data['parent_id'] = null;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $state = $this->form->getState();
        PlanResource::syncSinglePlanItemTranslations($this->getRecord(), $state['translations'] ?? []);
    }
}
