<?php

namespace App\Filament\Resources\AiKnowledgeItemResource\Pages;

use App\Filament\Resources\AiKnowledgeItemResource;
use App\Models\AiKnowledgeItem;
use App\Models\Language;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditAiKnowledgeItem extends LmpEditRecord
{
    protected static string $resource = AiKnowledgeItemResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var AiKnowledgeItem $record */
        $record = $this->getRecord();
        $record->load('translations');
        $data['translations'] = [];

        foreach (Language::query()->with('locale')->orderBy('id')->get() as $lang) {
            $trans = $record->translations->firstWhere('language_id', $lang->id);
            $data['translations'][$lang->id] = $trans ? [
                'title' => $trans->title,
                'content' => $trans->content,
                'content_short' => $trans->content_short ?? '',
                'url' => $trans->url ?? '',
                'tags' => is_array($trans->tags) ? implode(', ', $trans->tags) : '',
            ] : [
                'title' => '',
                'content' => '',
                'content_short' => '',
                'url' => '',
                'tags' => '',
            ];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return Arr::except($data, ['translations']);
    }

    protected function afterSave(): void
    {
        /** @var AiKnowledgeItem $record */
        $record = $this->getRecord();
        $translations = $this->form->getState()['translations'] ?? [];

        AiKnowledgeItemResource::syncTranslationsFromForm($record, $translations, false);
    }
}
