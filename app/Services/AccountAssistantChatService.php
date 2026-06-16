<?php

namespace App\Services;

use App\Support\AccountAssistantContext;
use App\Support\OpenAiUserFacingMessage;
use GuzzleHttp\Client as GuzzleClient;
use InvalidArgumentException;
use OpenAI\Client;
use RuntimeException;
use Throwable;

/**
 * RAG chat for authenticated account users using ai_knowledge_* embeddings.
 */
final class AccountAssistantChatService
{
    public function __construct(
        private readonly AiKnowledgeRetrievalService $retrievalService,
        private readonly ?Client $client = null,
    ) {}

    /**
     * @param  list<array{role: string, content: string}>  $history
     * @return array{
     *   answer: string,
     *   sources: list<array{key: string, title: string, url: ?string}>,
     *   embedding_model: string,
     *   chat_model: string,
     *   embedding_prompt_tokens: ?int,
     *   chat_prompt_tokens: ?int,
     *   chat_completion_tokens: ?int,
     *   chat_total_tokens: ?int,
     *   total_tokens: ?int,
     *   source_keys: list<string>,
     * }
     */
    public function ask(
        string $question,
        AccountAssistantContext $context,
        array $history = [],
    ): array {
        $question = trim($question);
        if ($question === '') {
            throw new InvalidArgumentException('Question cannot be empty.');
        }

        $retrieval = $this->retrievalService->retrieve(
            question: $question,
            languageId: $context->languageId,
            roleTag: $context->roleTag,
        );

        $contextBlock = $this->buildKnowledgeContextBlock($retrieval['articles']);
        $systemPrompt = trim((string) config('assistant.system_prompt'));

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        if ($contextBlock !== '') {
            $messages[] = [
                'role' => 'system',
                'content' => "Base de conocimiento (usa solo esto como fuente factual):\n\n" . $contextBlock,
            ];
        } else {
            $messages[] = [
                'role' => 'system',
                'content' => 'Base de conocimiento: (sin artículos recuperados para esta consulta). Indica que no tienes información en la documentación disponible y sugiere contactar al soporte de la plataforma.',
            ];
        }

        foreach ($history as $turn) {
            $role = $turn['role'] === 'assistant' ? 'assistant' : 'user';
            $content = trim((string) ($turn['content'] ?? ''));
            if ($content === '') {
                continue;
            }
            $messages[] = ['role' => $role, 'content' => $content];
        }

        $messages[] = ['role' => 'user', 'content' => $question];

        $model = (string) config('services.openai.chat_model', 'gpt-4o-mini');

        try {
            $response = $this->client()->chat()->create([
                'model' => $model,
                'temperature' => 0.2,
                'messages' => $messages,
            ]);
        } catch (Throwable $e) {
            throw new RuntimeException(OpenAiUserFacingMessage::from($e), 0, $e);
        }

        $answer = trim((string) ($response->choices[0]->message->content ?? ''));
        if ($answer === '') {
            throw new RuntimeException('OpenAI returned an empty assistant response.');
        }

        $usage = $response->usage ?? null;
        $chatPrompt = $usage?->promptTokens ?? $usage?->prompt_tokens ?? null;
        $chatCompletion = $usage?->completionTokens ?? $usage?->completion_tokens ?? null;
        $chatTotal = $usage?->totalTokens ?? $usage?->total_tokens ?? null;

        $embeddingTokens = $retrieval['query_embedding_tokens'] ?? null;
        $totalTokens = null;
        if ($chatTotal !== null || $embeddingTokens !== null) {
            $totalTokens = (int) ($chatTotal ?? 0) + (int) ($embeddingTokens ?? 0);
        }

        $sources = array_map(fn (array $article): array => [
            'key' => $article['item_key'],
            'title' => $article['title'],
            'url' => $article['url'],
        ], $retrieval['articles']);

        $sourceKeys = array_values(array_filter(array_map(
            fn (array $article) => $article['item_key'] !== '' ? $article['item_key'] : null,
            $retrieval['articles'],
        )));

        return [
            'answer' => $answer,
            'sources' => $sources,
            'embedding_model' => $retrieval['embedding_model'],
            'chat_model' => (string) ($response->model ?? $model),
            'embedding_prompt_tokens' => $embeddingTokens,
            'chat_prompt_tokens' => $chatPrompt !== null ? (int) $chatPrompt : null,
            'chat_completion_tokens' => $chatCompletion !== null ? (int) $chatCompletion : null,
            'chat_total_tokens' => $chatTotal !== null ? (int) $chatTotal : null,
            'total_tokens' => $totalTokens,
            'source_keys' => $sourceKeys,
        ];
    }

    /**
     * @param  list<array{title: string, content_short: ?string, content: string, url: ?string}>  $articles
     */
    private function buildKnowledgeContextBlock(array $articles): string
    {
        if ($articles === []) {
            return '';
        }

        $parts = [];
        foreach ($articles as $index => $article) {
            $n = $index + 1;
            $body = trim($article['content']);
            if (filled($article['content_short'] ?? null)) {
                $body = trim((string) $article['content_short']) . "\n\n" . $body;
            }
            $urlLine = filled($article['url'] ?? null) ? "\nURL: {$article['url']}" : '';
            $parts[] = "--- Artículo {$n}: {$article['title']} ---\n{$body}{$urlLine}";
        }

        return implode("\n\n", $parts);
    }

    private function client(): Client
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $key = trim((string) config('services.openai.api_key'));
        if ($key === '') {
            throw new RuntimeException('OPENAI_API_KEY is not configured.');
        }

        return \OpenAI::factory()
            ->withApiKey($key)
            ->withHttpClient(new GuzzleClient([
                'timeout' => 90,
                'http_errors' => true,
            ]))
            ->make();
    }
}
