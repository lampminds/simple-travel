<?php

namespace App\Services;

use App\Models\AiKnowledgeTranslation;

/**
 * Retrieves knowledge articles by embedding similarity (cosine) with role/keyword boosts.
 */
final class AiKnowledgeRetrievalService
{
    private const ROLE_MATCH_BOOST = 0.06;

    private const KEYWORD_MATCH_BOOST = 0.04;

    private const MAX_KEYWORD_BOOST = 0.16;

    public function __construct(
        private readonly EmbeddingService $embeddingService,
    ) {}

    /**
     * @return array{
     *   query_embedding_tokens: ?int,
     *   embedding_model: string,
     *   articles: list<array{
     *     translation_id: int,
     *     item_key: string,
     *     title: string,
     *     content_short: ?string,
     *     content: string,
     *     url: ?string,
     *     score: float,
     *   }>
     * }
     */
    public function retrieve(
        string $question,
        int $languageId,
        ?string $roleTag = null,
        ?int $topK = null,
        ?float $minScore = null,
    ): array {
        $topK ??= (int) config('assistant.top_k', 5);
        $minScore ??= (float) config('assistant.min_score', 0.15);

        $embeddingResult = $this->embeddingService->generate($question);
        $queryVector = $embeddingResult['embedding'];
        $questionLower = mb_strtolower($question);

        $candidates = AiKnowledgeTranslation::query()
            ->with('knowledgeItem')
            ->where('language_id', $languageId)
            ->whereNotNull('embedding')
            ->whereHas('knowledgeItem', fn ($q) => $q->where('is_active', true))
            ->get();

        $scored = $candidates
            ->map(function (AiKnowledgeTranslation $row) use ($queryVector, $roleTag, $questionLower): array {
                $vector = $row->embedding;
                if (! is_array($vector) || $vector === []) {
                    return ['row' => $row, 'score' => -1.0];
                }

                $score = $this->cosineSimilarity($queryVector, $vector);
                $score += $this->roleBoost($row, $roleTag, $this->inferredRoleTagsFromQuestion($questionLower));
                $score += $this->keywordBoost($questionLower, $row);

                return [
                    'row' => $row,
                    'score' => $score,
                ];
            })
            ->sortByDesc('score')
            ->values();

        $aboveMin = $scored->filter(fn (array $item) => $item['score'] >= $minScore);
        $selected = $aboveMin->take(max(1, $topK));

        // If nothing clears the threshold, still send the best matches to the model.
        if ($selected->isEmpty()) {
            $selected = $scored->take(max(1, $topK));
        }

        $articles = $selected->map(function (array $item): array {
            /** @var AiKnowledgeTranslation $row */
            $row = $item['row'];

            return [
                'translation_id' => (int) $row->id,
                'item_key' => (string) ($row->knowledgeItem?->key ?? ''),
                'title' => (string) $row->title,
                'content_short' => $row->content_short,
                'content' => (string) $row->content,
                'url' => $row->url,
                'score' => round((float) $item['score'], 4),
            ];
        })->values()->all();

        return [
            'query_embedding_tokens' => $embeddingResult['prompt_tokens'] ?? null,
            'embedding_model' => (string) $embeddingResult['model'],
            'articles' => $articles,
        ];
    }

    /**
     * @param  list<string>  $inferredRoleTags
     */
    private function roleBoost(AiKnowledgeTranslation $row, ?string $roleTag, array $inferredRoleTags): float
    {
        $tags = $row->tags;
        if (! is_array($tags) || $tags === []) {
            return 0.0;
        }

        $normalized = array_map(fn ($t) => mb_strtolower(trim((string) $t)), $tags);

        if (in_array('todos', $normalized, true)) {
            return self::ROLE_MATCH_BOOST;
        }

        $sessionRole = $roleTag !== null && $roleTag !== '' ? mb_strtolower($roleTag) : null;
        if ($sessionRole !== null && in_array($sessionRole, $normalized, true)) {
            return self::ROLE_MATCH_BOOST;
        }

        foreach ($inferredRoleTags as $inferred) {
            if (in_array(mb_strtolower($inferred), $normalized, true)) {
                return self::ROLE_MATCH_BOOST;
            }
        }

        return 0.0;
    }

    /**
     * @return list<string>
     */
    private function inferredRoleTagsFromQuestion(string $questionLower): array
    {
        $tags = [];

        if (preg_match('/\b(prestador|proveedor|hotel|habitacion|habitaciones|alojamiento|hoteleria|hotelería)\b/u', $questionLower)) {
            $tags[] = 'prestador';
        }

        if (preg_match('/\b(operador|paquete|paquetes)\b/u', $questionLower)) {
            $tags[] = 'operador';
        }

        if (preg_match('/\b(agencia|agencias|reserva|reservas)\b/u', $questionLower)) {
            $tags[] = 'agencia';
        }

        return array_values(array_unique($tags));
    }

    private function keywordBoost(string $questionLower, AiKnowledgeTranslation $row): float
    {
        $haystack = mb_strtolower(
            (string) $row->title . ' ' . (string) ($row->content_short ?? '') . ' ' . implode(' ', is_array($row->tags) ? $row->tags : [])
        );

        $keywords = [
            'hotel', 'habitacion', 'habitaciones', 'alojamiento', 'catalogo', 'catálogo',
            'prestador', 'operador', 'agencia', 'paquete', 'paquetes', 'reserva', 'reservas',
            'precio', 'precios', 'relacion', 'relaciones', 'cupos', 'traslado', 'servicio', 'servicios',
        ];

        $boost = 0.0;
        foreach ($keywords as $keyword) {
            if (str_contains($questionLower, $keyword) && str_contains($haystack, $keyword)) {
                $boost += self::KEYWORD_MATCH_BOOST;
            }
        }

        return min(self::MAX_KEYWORD_BOOST, $boost);
    }

    /**
     * @param  list<float>  $a
     * @param  list<float>  $b
     */
    private function cosineSimilarity(array $a, array $b): float
    {
        $length = min(count($a), count($b));
        if ($length === 0) {
            return 0.0;
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < $length; $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }

        if ($normA <= 0.0 || $normB <= 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }
}
