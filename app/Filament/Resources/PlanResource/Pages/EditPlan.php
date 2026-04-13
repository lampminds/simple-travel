<?php

namespace App\Filament\Resources\PlanResource\Pages;

use App\Filament\Resources\PlanResource;
use App\Models\Language;
use App\Models\Plan;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditPlan extends LmpEditRecord
{
    protected static string $resource = PlanResource::class;

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
            $data['translations'][$lang->id] = $trans ? [
                'price' => $trans->price,
                'name' => $trans->name ?? '',
                'description' => $trans->description,
            ] : ['price' => null, 'name' => '', 'description' => null];
        }

        $record->load(['allItems.translations']);
        $data['plan_items'] = $record->allItems
            ->sortBy(fn ($item): array => [$item->parent_id === null ? 0 : 1, $item->sort_order])
            ->values()
            ->map(function ($item) use ($languages) {
                $trans = [];
                foreach ($languages as $lang) {
                    $t = $item->translations->firstWhere('language_id', $lang->id);
                    $trans[$lang->id] = ['text' => $t?->text ?? ''];
                }

                return [
                    'id' => $item->id,
                    'client_key' => (string) $item->id,
                    'parent_ref' => $item->parent_id ? (string) $item->parent_id : null,
                    'active' => $item->active,
                    'translations' => $trans,
                ];
            })
            ->all();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return Arr::except($data, ['translations', 'plan_items']);
    }

    protected function afterSave(): void
    {
        $state = $this->form->getState();
        $this->syncTranslations($this->getRecord(), $state['translations'] ?? []);
        PlanResource::syncPlanItems($this->getRecord(), $state['plan_items'] ?? []);
    }

    protected function syncTranslations(Plan $record, array $translations): void
    {
        $record->translations()->delete();
        foreach ($translations as $languageId => $row) {
            $name = $row['name'] ?? '';
            $description = $row['description'] ?? null;
            $price = isset($row['price']) && $row['price'] !== '' && $row['price'] !== null ? $row['price'] : null;
            if ($name !== '' || $description !== null || $price !== null) {
                $record->translations()->create([
                    'language_id' => $languageId,
                    'price' => $price,
                    'name' => $name,
                    'description' => $description,
                ]);
            }
        }
    }
}
