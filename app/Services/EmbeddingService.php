<?php

namespace App\Services;

use GuzzleHttp\Client as GuzzleClient;
use InvalidArgumentException;
use OpenAI\Client;

class EmbeddingService
{
    /** OpenAI keys are short; much longer values usually mean a misconfigured .env. */
    private const API_KEY_MAX_LENGTH = 512;

    public function __construct(
        protected ?Client $client = null,
    ) {}

    /**
     * @return array{
     *   embedding: list<float>,
     *   model: string,
     *   version: string,
     *   prompt_tokens: ?int,
     *   total_tokens: ?int,
     * }
     */
    public function generate(string $text): array
    {
        if (trim($text) === '') {
            throw new InvalidArgumentException('Cannot generate an embedding for empty text.');
        }

        $model = (string) config('services.openai.embedding_model', 'text-embedding-3-small');

        $response = $this->client()->embeddings()->create([
            'model' => $model,
            'input' => $text,
        ]);

        // openai-php/client v0.19+: CreateResponse exposes ->embeddings[], not ->data[]
        $vector = $response->embeddings[0]->embedding;
        $usage = $response->usage ?? null;
        $promptTokens = $usage?->promptTokens ?? $usage?->prompt_tokens ?? null;
        $totalTokens = $usage?->totalTokens ?? $usage?->total_tokens ?? null;

        return [
            'embedding' => $vector,
            'model' => $response->model ?? $model,
            'version' => (string) config('services.openai.embedding_version', 'v1'),
            'prompt_tokens' => $promptTokens !== null ? (int) $promptTokens : null,
            'total_tokens' => $totalTokens !== null ? (int) $totalTokens : null,
        ];
    }

    public function buildText(string $title, ?string $contentShort, string $content): string
    {
        return trim(
            $title . "\n" .
            ($contentShort ? $contentShort . "\n" : '') .
            $content
        );
    }

    protected function client(): Client
    {
        return $this->client ??= $this->makeClient();
    }

    protected function makeClient(): Client
    {
        return \OpenAI::factory()
            ->withApiKey($this->resolveApiKey())
            ->withHttpClient(new GuzzleClient([
                'timeout' => 60,
                'http_errors' => true,
            ]))
            ->make();
    }

    protected function resolveApiKey(): string
    {
        $key = trim((string) config('services.openai.api_key'));

        if ($key === '') {
            throw new InvalidArgumentException('OPENAI_API_KEY is not configured.');
        }

        if (strlen($key) > self::API_KEY_MAX_LENGTH) {
            throw new InvalidArgumentException(
                'OPENAI_API_KEY is unusually long (' . strlen($key) . ' characters). '
                . 'Use only the sk-... key from platform.openai.com. '
                . 'A very long value in Authorization headers causes OpenAI error 431 (request headers too large).'
            );
        }

        return $key;
    }
}
