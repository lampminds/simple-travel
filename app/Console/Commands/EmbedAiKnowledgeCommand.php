<?php

namespace App\Console\Commands;

use App\Models\AiKnowledgeTranslation;
use App\Services\EmbeddingService;
use Illuminate\Console\Command;
use OpenAI\Exceptions\ErrorException;
use Throwable;

class EmbedAiKnowledgeCommand extends Command
{
    protected $signature = 'ai:knowledge:embed
                            {--language=2 : cat_languages.id to process (default 2 = Spanish)}
                            {--missing-only : Only rows with a null embedding}
                            {--key= : Restrict to one ai_knowledge_items.key}';

    protected $description = 'Generate OpenAI embeddings for AI knowledge base translations';

    public function handle(EmbeddingService $embeddingService): int
    {
        $apiKey = trim((string) config('services.openai.api_key'));

        if ($apiKey === '') {
            $this->error('OPENAI_API_KEY is not set in .env');

            return self::FAILURE;
        }

        $this->line('OPENAI_API_KEY length: ' . strlen($apiKey) . ' characters');

        if (strlen($apiKey) > 512) {
            $this->error('The API key looks too long. Paste only the sk-... value from platform.openai.com.');
            $this->error('Oversized keys trigger OpenAI error 431 (request headers too large).');

            return self::FAILURE;
        }

        $languageId = (int) $this->option('language');
        $keyFilter = $this->option('key');

        $query = AiKnowledgeTranslation::query()
            ->with('knowledgeItem')
            ->where('language_id', $languageId)
            ->when($this->option('missing-only'), fn ($q) => $q->whereNull('embedding'))
            ->when($keyFilter, function ($q) use ($keyFilter): void {
                $q->whereHas('knowledgeItem', fn ($itemQ) => $itemQ->where('key', $keyFilter));
            })
            ->orderBy('id');

        $translations = $query->get();

        if ($translations->isEmpty()) {
            $this->warn('No knowledge translations matched the filters.');

            return self::SUCCESS;
        }

        $this->info('Embedding ' . $translations->count() . ' translation(s)...');

        $failures = 0;

        foreach ($translations as $translation) {
            $itemKey = $translation->knowledgeItem?->key ?? ('#' . $translation->ai_knowledge_item_id);
            $label = $itemKey . ' [lang ' . $translation->language_id . ']';

            $text = $embeddingService->buildText(
                (string) $translation->title,
                $translation->content_short,
                (string) $translation->content,
            );

            if (trim($text) === '') {
                $this->warn("Skipping {$label}: empty text.");
                continue;
            }

            try {
                $result = $embeddingService->generate($text);

                $translation->forceFill([
                    'embedding' => $result['embedding'],
                    'embedding_model' => $result['model'],
                    'embedding_version' => $result['version'],
                ])->saveQuietly();

                $this->line("OK {$label}");
            } catch (ErrorException $e) {
                $failures++;
                $this->error("FAIL {$label}: " . $e->getMessage());

                if (str_contains(strtolower($e->getMessage()), 'header')) {
                    $this->newLine();
                    $this->error('Hint: verify OPENAI_API_KEY in .env (short sk-... string only).');
                    break;
                }
            } catch (Throwable $e) {
                $failures++;
                $this->error("FAIL {$label}: " . $e->getMessage());
            }
        }

        if ($failures > 0) {
            return self::FAILURE;
        }

        $this->info('Done.');

        return self::SUCCESS;
    }
}
