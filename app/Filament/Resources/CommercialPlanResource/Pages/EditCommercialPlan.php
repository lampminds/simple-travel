<?php

namespace App\Filament\Resources\CommercialPlanResource\Pages;

use App\Filament\Resources\CommercialPlanResource;
use App\Models\CommercialPlan;
use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditCommercialPlan extends LmpEditRecord
{
    protected static string $resource = CommercialPlanResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var CommercialPlan $record */
        $record = $this->getRecord();
        $record->load('translations');
        $data['translations'] = [];
        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $trans = $record->translations->firstWhere('language_id', $lang->id);
            $data['translations'][$lang->id] = $trans ? [
                'name' => $trans->name ?? '',
                'description' => $trans->description,
            ] : ['name' => '', 'description' => null];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return Arr::except($data, ['translations', 'accountTypes']);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record = parent::handleRecordUpdate($record, $data);
        $this->form->model($record)->saveRelationships();

        return $record;
    }

    protected function afterSave(): void
    {
        $state = $this->form->getState();
        CommercialPlanResource::syncTranslationsFromForm($this->getRecord(), $state['translations'] ?? []);
    }
}
