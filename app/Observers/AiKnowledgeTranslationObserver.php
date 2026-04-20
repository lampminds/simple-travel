<?php

namespace App\Observers;

use App\Models\AiKnowledgeTranslation;
use App\Services\EmbeddingService;

class AiKnowledgeTranslationObserver
{
    public function saving(AiKnowledgeTranslation $model): void
    {
        if (! $model->isDirty(['title', 'content', 'content_short'])) {
            return;
        }

        $service = app(EmbeddingService::class);

        $text = $service->buildText(
            $model->title,
            $model->content_short,
            $model->content
        );

        if (trim($text) === '') {
            $model->embedding = null;
            $model->embedding_model = null;
            $model->embedding_version = null;

            return;
        }

        $result = $service->generate($text);

        $model->embedding = $result['embedding'];
        $model->embedding_model = $result['model'];
        $model->embedding_version = $result['version'];
    }
}
