<?php

namespace App\Services\Translation;

use App\Models\Language;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    /**
     * Translate name/description from one source language to all other languages.
     *
     * @return array{
     *   ok: bool,
     *   message: string,
     *   translations: array<string, array{name: ?string, description: ?string}>,
     *   providers: array<string, array{name?: string, description?: string}>,
     *   failures: array<int, array{to: string, reason: string}>
     * }
     */
    public function translateFromLanguage(int $sourceLanguageId, array $translationsPayload, ?int $userId = null): array
    {
        $languages = Language::query()
            ->with('locale')
            ->get()
            ->keyBy('id');

        $sourceLanguage = $languages->get($sourceLanguageId);
        if (! $sourceLanguage) {
            return $this->fail('Invalid source language.', []);
        }

        $sourceCode = $this->resolveLanguageCode($sourceLanguage);
        $sourceName = trim((string) data_get($translationsPayload, "{$sourceLanguageId}.name", ''));
        $sourceDescription = trim((string) data_get($translationsPayload, "{$sourceLanguageId}.description", ''));

        if (! $sourceCode) {
            return $this->fail('The selected source language has no translation code configured.', []);
        }

        if ($sourceName === '' && $sourceDescription === '') {
            return $this->fail('Please fill the name or description in the selected source language.', []);
        }

        $translations = [];
        $providers = [];
        $failures = [];

        foreach ($languages as $language) {
            if ((int) $language->id === $sourceLanguageId) {
                continue;
            }

            $targetCode = $this->resolveLanguageCode($language);
            if (! $targetCode) {
                continue;
            }

            try {
                $targetPayload = [
                    'name' => null,
                    'description' => null,
                ];
                $fieldProviders = [];
                $fieldFailureReasons = [];

                if ($sourceName !== '') {
                    if ($this->shouldCopyWithoutTranslation($sourceName)) {
                        $targetPayload['name'] = $sourceName;
                        $fieldProviders['name'] = 'copied';
                    } else {
                        $nameResult = $this->translateText($sourceName, $sourceCode, $targetCode);
                        $targetPayload['name'] = $nameResult['translated'] ?? null;
                        if ($targetPayload['name']) {
                            $fieldProviders['name'] = $nameResult['provider'] ?? 'unknown';
                        } else {
                            $fieldFailureReasons[] = 'name: ' . ($nameResult['reason'] ?? 'translation failed');
                        }
                    }
                }

                if ($sourceDescription !== '') {
                    if ($this->shouldCopyWithoutTranslation($sourceDescription)) {
                        $targetPayload['description'] = $sourceDescription;
                        $fieldProviders['description'] = 'copied';
                    } else {
                        $descriptionResult = $this->translateText($sourceDescription, $sourceCode, $targetCode);
                        $targetPayload['description'] = $descriptionResult['translated'] ?? null;
                        if ($targetPayload['description']) {
                            $fieldProviders['description'] = $descriptionResult['provider'] ?? 'unknown';
                        } else {
                            $fieldFailureReasons[] = 'description: ' . ($descriptionResult['reason'] ?? 'translation failed');
                        }
                    }
                }

                if (($targetPayload['name'] === null || $targetPayload['name'] === '')
                    && ($targetPayload['description'] === null || $targetPayload['description'] === '')
                ) {
                    $failures[] = [
                        'to' => $targetCode,
                        'reason' => implode(' | ', $fieldFailureReasons) ?: 'Unknown translation error.',
                    ];
                    continue;
                }

                $translations[(string) $language->id] = $targetPayload;
                $providers[(string) $language->id] = $fieldProviders;
            } catch (\Throwable $e) {
                $failures[] = [
                    'to' => $targetCode,
                    'reason' => $e->getMessage(),
                ];
            }
        }

        if (empty($translations)) {
            $firstFailure = $failures[0] ?? null;
            $failureMessage = $firstFailure
                ? 'No translation could be generated. First failure (' . $sourceCode . '->' . $firstFailure['to'] . '): ' . $firstFailure['reason']
                : 'No translation could be generated. No providers returned translations.';

            Log::warning('Service wizard translation failed', [
                'user_id' => $userId,
                'source_language_id' => $sourceLanguage->id,
                'source_code' => $sourceCode,
                'target_count' => max(0, $languages->count() - 1),
                'providers' => $providers,
                'failures' => $failures,
            ]);

            return $this->fail($failureMessage, $failures);
        }

        Log::info('Service wizard translation completed', [
            'user_id' => $userId,
            'source_language_id' => $sourceLanguage->id,
            'source_code' => $sourceCode,
            'translated_count' => count($translations),
            'providers' => $providers,
            'failures' => $failures,
        ]);

        return [
            'ok' => true,
            'message' => '',
            'translations' => $translations,
            'providers' => $providers,
            'failures' => $failures,
        ];
    }

    protected function fail(string $message, array $failures): array
    {
        return [
            'ok' => false,
            'message' => $message,
            'translations' => [],
            'providers' => [],
            'failures' => $failures,
        ];
    }

    protected function shouldCopyWithoutTranslation(string $text): bool
    {
        $trimmed = trim($text);
        if ($trimmed === '') {
            return true;
        }

        return ! preg_match('/\p{L}/u', $trimmed);
    }

    protected function resolveLanguageCode(Language $language): ?string
    {
        $primary = $language->locale?->primaryLanguageTag() ?? '';
        if ($primary !== '') {
            return $this->normalizeLanguageCode($primary);
        }

        return null;
    }

    protected function normalizeLanguageCode(string $code): string
    {
        $code = strtolower(trim($code));

        $iso3ToIso2 = [
            'eng' => 'en',
            'spa' => 'es',
            'por' => 'pt',
            'fra' => 'fr',
            'deu' => 'de',
            'ita' => 'it',
            'nld' => 'nl',
            'cat' => 'ca',
            'eus' => 'eu',
            'glg' => 'gl',
            'ron' => 'ro',
            'rus' => 'ru',
            'ukr' => 'uk',
            'zho' => 'zh',
            'jpn' => 'ja',
            'kor' => 'ko',
            'hin' => 'hi',
            'ara' => 'ar',
            'tur' => 'tr',
            'swe' => 'sv',
            'nor' => 'no',
            'dan' => 'da',
            'fin' => 'fi',
            'pol' => 'pl',
            'ces' => 'cs',
            'slk' => 'sk',
            'hun' => 'hu',
            'ell' => 'el',
            'heb' => 'he',
            'tha' => 'th',
            'ind' => 'id',
            'msa' => 'ms',
            'vie' => 'vi',
        ];

        if (isset($iso3ToIso2[$code])) {
            return $iso3ToIso2[$code];
        }

        if (str_contains($code, '_')) {
            $code = str_replace('_', '-', $code);
        }

        if (strlen($code) > 2 && str_contains($code, '-')) {
            return substr($code, 0, 2);
        }

        return $code;
    }

    protected function translateText(string $text, string $sourceCode, string $targetCode): array
    {
        $sourceCode = strtolower(trim($sourceCode));
        $targetCode = strtolower(trim($targetCode));
        $text = trim($text);

        if ($text === '' || $sourceCode === '' || $targetCode === '' || $sourceCode === $targetCode) {
            return [
                'translated' => null,
                'provider' => null,
                'reason' => 'Invalid translation parameters.',
            ];
        }

        $myMemoryUrl = rtrim((string) config('services.translation.url', 'https://api.mymemory.translated.net/get'), '/');
        $mymemoryError = null;
        try {
            $response = Http::timeout(12)->get($myMemoryUrl, [
                'q' => $text,
                'langpair' => $sourceCode . '|' . $targetCode,
            ]);

            if ($response->successful()) {
                $translated = trim((string) data_get($response->json(), 'responseData.translatedText', ''));
                if ($translated !== '' && strcasecmp($translated, $text) !== 0) {
                    return [
                        'translated' => $translated,
                        'provider' => 'mymemory',
                        'reason' => null,
                    ];
                }
                $mymemoryError = 'MyMemory returned empty/same translation.';
            } else {
                $mymemoryError = 'MyMemory HTTP ' . $response->status() . '.';
            }
        } catch (\Throwable $e) {
            $mymemoryError = 'MyMemory exception: ' . $e->getMessage();
        }

        $googleError = null;
        try {
            $response = Http::timeout(12)->get('https://translate.googleapis.com/translate_a/single', [
                'client' => 'gtx',
                'sl' => $sourceCode,
                'tl' => $targetCode,
                'dt' => 't',
                'q' => $text,
            ]);

            if (! $response->successful()) {
                $googleError = 'Google HTTP ' . $response->status() . '.';
                return [
                    'translated' => null,
                    'provider' => null,
                    'reason' => trim(($mymemoryError ?? 'MyMemory failed.') . ' ' . $googleError),
                ];
            }

            $payload = $response->json();
            if (! is_array($payload) || ! isset($payload[0]) || ! is_array($payload[0])) {
                $googleError = 'Google returned unexpected payload.';
                return [
                    'translated' => null,
                    'provider' => null,
                    'reason' => trim(($mymemoryError ?? 'MyMemory failed.') . ' ' . $googleError),
                ];
            }

            $parts = [];
            foreach ($payload[0] as $segment) {
                if (is_array($segment) && isset($segment[0]) && is_string($segment[0])) {
                    $parts[] = $segment[0];
                }
            }

            $translated = trim(implode('', $parts));
            if ($translated === '' || strcasecmp($translated, $text) === 0) {
                $googleError = 'Google returned empty/same translation.';
                return [
                    'translated' => null,
                    'provider' => null,
                    'reason' => trim(($mymemoryError ?? 'MyMemory failed.') . ' ' . $googleError),
                ];
            }

            return [
                'translated' => $translated,
                'provider' => 'google_fallback',
                'reason' => null,
            ];
        } catch (\Throwable $e) {
            $googleError = 'Google exception: ' . $e->getMessage();
            return [
                'translated' => null,
                'provider' => null,
                'reason' => trim(($mymemoryError ?? 'MyMemory failed.') . ' ' . $googleError),
            ];
        }
    }
}

