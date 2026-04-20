<?php

namespace App\Services;

use InvalidArgumentException;
use OpenAI\Client;

class EmbeddingService
{
    public function __construct(
        protected ?Client $client = null,
    ) {
        $this->client ??= \OpenAI::client((string) config('services.openai.api_key'));
    }

    /**
     * @return array{embedding: list<float>, model: string, version: string}
     */
    public function generate(string $text): array
    {
        if (trim($text) === '') {
            throw new InvalidArgumentException('Cannot generate an embedding for empty text.');
        }

        $model = (string) config('services.openai.embedding_model', 'text-embedding-3-small');

        $response = $this->client->embeddings()->create([
            'model' => $model,
            'input' => $text,
        ]);

        // openai-php/client v0.19+: CreateResponse exposes ->embeddings[], not ->data[]
        $vector = $response->embeddings[0]->embedding;

        return [
            'embedding' => $vector,
            'model' => $response->model ?? $model,
            'version' => (string) config('services.openai.embedding_version', 'v1'),
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
}
