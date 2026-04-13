<?php

namespace App\Filament\Resources\PlanItemResource\Pages;

use App\Filament\Resources\PlanItemResource;
use App\Filament\Resources\PlanResource;
use App\Models\Language;
use App\Models\PlanItem;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreatePlanItem extends LmpCreateRecord
{
    protected static string $resource = PlanItemResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function fillForm(): void
    {
        parent::fillForm();
        $translations = [];
        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $translations[$lang->id] = ['text' => ''];
        }
        $state = $this->form->getRawState() ?? [];
        $state['translations'] = $translations;
        $this->form->fill($state);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = Arr::except($data, ['translations']);

        if (isset($data['parent_id']) && $data['parent_id'] === '') {
            $data['parent_id'] = null;
        }

        $planId = (int) ($data['plan_id'] ?? 0);
        if ($planId > 0 && (! isset($data['sort_order']) || $data['sort_order'] === '' || $data['sort_order'] === null)) {
            $data['sort_order'] = (int) PlanItem::query()
                ->where('plan_id', $planId)
                ->where('parent_id', $data['parent_id'] ?? null)
                ->max('sort_order') + 1;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $state = $this->form->getState();
        PlanResource::syncSinglePlanItemTranslations($this->getRecord(), $state['translations'] ?? []);
    }
}
