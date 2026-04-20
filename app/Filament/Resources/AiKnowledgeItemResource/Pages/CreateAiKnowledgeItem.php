<?php

namespace App\Filament\Resources\AiKnowledgeItemResource\Pages;

use App\Filament\Resources\AiKnowledgeItemResource;
use App\Models\AiKnowledgeItem;
use App\Models\Language;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateAiKnowledgeItem extends LmpCreateRecord
{
    protected static string $resource = AiKnowledgeItemResource::class;

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
                'title' => '',
                'content' => '',
                'content_short' => '',
                'url' => '',
                'tags' => '',
            ];
        }
        $state = $this->form->getRawState() ?? [];
        $state['translations'] = $translations;
        $this->form->fill($state);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return Arr::except($data, ['translations']);
    }

    protected function afterCreate(): void
    {
        /** @var AiKnowledgeItem $record */
        $record = $this->getRecord();
        $translations = $this->form->getState()['translations'] ?? [];

        AiKnowledgeItemResource::syncTranslationsFromForm($record, $translations, true);
    }
}
