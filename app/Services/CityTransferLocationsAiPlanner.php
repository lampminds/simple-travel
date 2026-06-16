<?php

namespace App\Services;

use App\Models\LmpCity;
use App\Models\ServiceTransferLocationType;
use App\Support\AiUsageContext;
use App\Support\OpenAiUserFacingMessage;
use App\Support\SystemAccount;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use OpenAI\Client;
use RuntimeException;

/**
 * Uses OpenAI to propose transfer pickup/drop-off points for a city (structured JSON).
 */
final class CityTransferLocationsAiPlanner
{
    public function __construct(
        private readonly ?Client $client = null,
        private readonly ?AiUsageLogger $usageLogger = null,
    ) {
    }

    /**
     * @return list<array{type_code: string, name: string, airport_code: ?string}>
     */
    public function suggestForCity(
        LmpCity $city,
        Collection $allowedTypeCodes,
        int $maxSuggestions = 30,
        ?string $additionalContext = null,
        string $outputLanguage = 'es',
    ): array {
        $apiKey = trim((string) config('services.openai.api_key', ''));
        if ($apiKey === '') {
            throw new RuntimeException('OPENAI_API_KEY is not configured.');
        }

        $city->loadMissing('state.country');
        $cityLabel = trim((string) $city->name);
        $stateLabel = trim((string) ($city->state?->name ?? ''));
        $countryLabel = trim((string) ($city->state?->country?->name ?? ''));

        if ($cityLabel === '') {
            throw new InvalidArgumentException('City name is required.');
        }

        $typeList = $allowedTypeCodes->sort()->values()->implode(', ');
        $maxSuggestions = max(5, min(50, $maxSuggestions));
        $outputLanguage = in_array($outputLanguage, ['es', 'en', 'pt'], true) ? $outputLanguage : 'es';

        $contextBlock = $additionalContext !== null && trim($additionalContext) !== ''
            ? "\nAdditional operator notes:\n".trim($additionalContext)
            : '';

        $userPrompt = <<<PROMPT
Destination area for private transfer services:
- City: {$cityLabel}
- State/region: {$stateLabel}
- Country: {$countryLabel}
{$contextBlock}

Return a JSON object with key "locations" (array). Each item must have:
- "type_code": one of [{$typeList}]
- "name": short human-readable label in {$outputLanguage} (include IATA in parentheses when airport_code is set)
- "airport_code": uppercase IATA for airports only, otherwise null

Include the most useful real-world points for transfers: airports, bus terminals, downtown areas, major hotels, national parks, lakes, ski resorts, beaches, viewpoints, meeting points, scenic routes, etc. Prefer specific proper names. No duplicates. Maximum {$maxSuggestions} items.
PROMPT;

        $client = $this->client ?? \OpenAI::client($apiKey);
        $model = (string) config('services.openai.chat_model', 'gpt-4o-mini');

        try {
            $response = $client->chat()->create([
                'model' => $model,
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.4,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a tourism operations assistant for Latin America transfer companies. Output valid JSON only.',
                    ],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
            ]);
        } catch (\Throwable $e) {
            throw new RuntimeException(OpenAiUserFacingMessage::from($e), 0, $e);
        }

        $raw = trim((string) ($response->choices[0]->message->content ?? ''));
        if ($raw === '') {
            throw new RuntimeException('OpenAI returned an empty response.');
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            throw new RuntimeException('OpenAI response is not valid JSON.');
        }

        $rows = $decoded['locations'] ?? null;
        if (! is_array($rows)) {
            throw new RuntimeException('OpenAI JSON must contain a "locations" array.');
        }

        $allowed = $allowedTypeCodes->flip();
        $normalized = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $typeCode = strtolower(trim((string) ($row['type_code'] ?? '')));
            $name = trim((string) ($row['name'] ?? ''));
            if ($typeCode === '' || $name === '' || ! $allowed->has($typeCode)) {
                continue;
            }

            $airportCode = $row['airport_code'] ?? null;
            $airportCode = is_string($airportCode) && $airportCode !== ''
                ? strtoupper(trim($airportCode))
                : null;

            $normalized[] = [
                'type_code' => $typeCode,
                'name' => $name,
                'airport_code' => $airportCode,
            ];
        }

        if ($normalized === []) {
            throw new RuntimeException('No valid locations were parsed from the AI response.');
        }

        return $normalized;
    }

    /**
     * Translates many location labels in one or two OpenAI calls (batched by size).
     *
     * @param  list<string>  $names  Labels in the source language, in list order
     * @param  list<string>  $targetLanguageCodes  ISO-style codes (en, es, pt) excluding source
     * @return list<array<string, string>>  Same length as $names; each entry maps lang code => translated label
     */
    public function translateLocationNamesBatch(
        array $names,
        string $sourceLanguageCode,
        array $targetLanguageCodes,
        ?AiUsageContext $usageContext = null,
    ): array {
        $names = array_values(array_map(static fn (string $n): string => trim($n), $names));
        $targetLanguageCodes = array_values(array_unique(array_filter(
            array_map(static fn (string $c): string => strtolower(trim($c)), $targetLanguageCodes),
            static fn (string $c): bool => in_array($c, ['en', 'es', 'pt'], true)
        )));

        $sourceLanguageCode = in_array($sourceLanguageCode, ['en', 'es', 'pt'], true)
            ? $sourceLanguageCode
            : 'es';

        if ($names === [] || $targetLanguageCodes === []) {
            return [];
        }

        $targetList = implode(', ', $targetLanguageCodes);
        $chunks = count($names) <= 25
            ? [$names]
            : array_slice(array_chunk($names, (int) ceil(count($names) / 2)), 0, 2);

        $merged = [];

        foreach ($chunks as $chunk) {
            $partial = $this->translateLocationNamesChunk(
                $chunk,
                $sourceLanguageCode,
                $targetLanguageCodes,
                $targetList,
                $usageContext,
            );
            foreach ($partial as $row) {
                $merged[] = $row;
            }
        }

        return $merged;
    }

    /**
     * @param  list<string>  $names
     * @param  list<string>  $targetLanguageCodes
     * @return list<array<string, string>>
     */
    private function translateLocationNamesChunk(
        array $names,
        string $sourceLanguageCode,
        array $targetLanguageCodes,
        string $targetList,
        ?AiUsageContext $usageContext = null,
    ): array {
        $apiKey = trim((string) config('services.openai.api_key', ''));
        if ($apiKey === '') {
            throw new RuntimeException('OPENAI_API_KEY is not configured.');
        }

        $encodedNames = json_encode($names, JSON_UNESCAPED_UNICODE);

        $userPrompt = <<<PROMPT
Translate tourism transfer location display names.

Source language: {$sourceLanguageCode}
Target languages: {$targetList}

Input "names" (array, keep the same order and length):
{$encodedNames}

Return JSON only with key "items": an array of objects. Each object must use language codes as keys ({$targetList}) and translated string values.

Rules:
- "items" must have exactly the same number of elements as "names"
- Each item must include every target language key ({$targetList})
- Keep proper nouns, hotel names, and IATA codes in parentheses unchanged when appropriate
- Natural phrasing for transfer pickup/drop-off lists
PROMPT;

        $client = $this->client ?? \OpenAI::client($apiKey);
        $model = (string) config('services.openai.chat_model', 'gpt-4o-mini');

        try {
            $response = $client->chat()->create([
                'model' => $model,
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.2,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You translate short place labels for a travel operations system. Output valid JSON only.',
                    ],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
            ]);
        } catch (\Throwable $e) {
            throw new RuntimeException(OpenAiUserFacingMessage::from($e), 0, $e);
        }

        $usage = $response->usage ?? null;
        $promptTokens = $usage?->promptTokens ?? $usage?->prompt_tokens ?? null;
        $completionTokens = $usage?->completionTokens ?? $usage?->completion_tokens ?? null;
        $totalTokens = $usage?->totalTokens ?? $usage?->total_tokens ?? null;
        $promptTokens = $promptTokens !== null ? (int) $promptTokens : null;
        $completionTokens = $completionTokens !== null ? (int) $completionTokens : null;
        $totalTokens = $totalTokens !== null ? (int) $totalTokens : null;
        $chatTotalTokens = ($promptTokens ?? 0) + ($completionTokens ?? 0);
        if ($totalTokens === null && $chatTotalTokens > 0) {
            $totalTokens = $chatTotalTokens;
        }

        $logger = $this->usageLogger ?? app(AiUsageLogger::class);
        $context = $usageContext ?? new AiUsageContext(
            userId: (int) (auth()->id() ?? SystemAccount::USER_ID),
            useSystemAccount: true,
            source: 'filament.lmp_city_transfer_locations',
        );
        $logger->logOpenAiTranslation(
            context: $context,
            summary: sprintf(
                'OpenAI translate %d location label(s) from %s to %s',
                count($names),
                $sourceLanguageCode,
                $targetList,
            ),
            chatModel: $model,
            chatPromptTokens: $promptTokens,
            chatCompletionTokens: $completionTokens,
            chatTotalTokens: $chatTotalTokens > 0 ? $chatTotalTokens : null,
            totalTokens: $totalTokens,
        );

        $raw = trim((string) ($response->choices[0]->message->content ?? ''));
        $decoded = json_decode($raw, true);
        if (! is_array($decoded) || ! is_array($decoded['items'] ?? null)) {
            throw new RuntimeException('OpenAI translation response must contain an "items" array.');
        }

        $items = $decoded['items'];
        $expected = count($names);
        if (count($items) !== $expected) {
            throw new RuntimeException(
                'OpenAI returned '.count($items).' translations but '.$expected.' were expected.'
            );
        }

        $normalized = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                $normalized[] = [];

                continue;
            }
            $row = [];
            foreach ($targetLanguageCodes as $code) {
                $value = trim((string) ($item[$code] ?? ''));
                if ($value !== '') {
                    $row[$code] = $value;
                }
            }
            $normalized[] = $row;
        }

        return $normalized;
    }

    /**
     * @return Collection<int, string> allowed type codes (active catalog)
     */
    public function activeTypeCodes(): Collection
    {
        return ServiceTransferLocationType::query()
            ->where('active', true)
            ->orderBy('sort_order')
            ->pluck('code');
    }
}
