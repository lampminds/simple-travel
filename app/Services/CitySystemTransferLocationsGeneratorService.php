<?php

namespace App\Services;

use App\Models\Language;
use App\Models\LmpCity;
use App\Models\ServiceTransferLocation;
use App\Models\ServiceTransferLocationTranslation;
use App\Models\ServiceTransferLocationType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Creates system-catalog transfer locations (account_id null) for a city, optionally via AI suggestions.
 */
final class CitySystemTransferLocationsGeneratorService
{
    public function __construct(
        private readonly CityTransferLocationsAiPlanner $planner,
    ) {
    }

    /**
     * @return array{
     *   created: int,
     *   skipped: int,
     *   removed: int,
     *   ai_count: int,
     *   translation_fallbacks: int,
     *   openai_calls: int
     * }
     */
    public function generateFromAi(
        LmpCity $city,
        bool $replaceExisting = false,
        bool $translateToOtherLanguages = true,
        ?int $sourceLanguageId = null,
        int $maxSuggestions = 30,
        ?string $additionalContext = null,
    ): array {
        $cityId = (int) $city->getKey();
        if ($cityId < 1) {
            throw new RuntimeException('Invalid city.');
        }

        $typeCodes = $this->planner->activeTypeCodes();
        if ($typeCodes->isEmpty()) {
            throw new RuntimeException('No active transfer location types in the catalog.');
        }

        $languages = Language::query()->with('locale')->orderBy('list_order')->orderBy('id')->get();
        $sourceLanguageId = $this->resolveSourceLanguageId($sourceLanguageId, $languages);
        $sourceLocaleCode = $this->localeCodeForLanguage($sourceLanguageId, $languages);

        $openaiCalls = 1;

        $suggestions = $this->planner->suggestForCity(
            $city,
            $typeCodes,
            $maxSuggestions,
            $additionalContext,
            $sourceLocaleCode,
        );

        $targetLanguages = $this->targetLanguagesExcludingSource($languages, $sourceLanguageId);
        $batchTranslations = [];

        if ($translateToOtherLanguages && $targetLanguages->isNotEmpty()) {
            $targetCodes = $targetLanguages->pluck('code')->values()->all();
            $nameList = array_map(static fn (array $row): string => (string) $row['name'], $suggestions);
            $translationChunks = count($nameList) <= 25 ? 1 : 2;
            $openaiCalls += $translationChunks;

            $batchTranslations = $this->planner->translateLocationNamesBatch(
                $nameList,
                $sourceLocaleCode,
                $targetCodes,
            );
        }

        $typeIds = ServiceTransferLocationType::query()
            ->whereIn('code', $typeCodes->all())
            ->pluck('id', 'code')
            ->all();

        $removed = 0;
        $created = 0;
        $skipped = 0;
        $translationFallbacks = 0;

        DB::transaction(function () use (
            $cityId,
            $replaceExisting,
            $suggestions,
            $typeIds,
            $sourceLanguageId,
            $translateToOtherLanguages,
            $batchTranslations,
            $languages,
            &$removed,
            &$created,
            &$skipped,
            &$translationFallbacks,
        ): void {
            if ($replaceExisting) {
                $removed = $this->deleteSystemCatalogForCity($cityId);
            }

            foreach ($suggestions as $index => $row) {
                $typeId = (int) ($typeIds[$row['type_code']] ?? 0);
                if ($typeId < 1) {
                    $skipped++;

                    continue;
                }

                $slug = $this->slugFromDisplayName($row['name']);
                if (ServiceTransferLocation::query()
                    ->whereNull('account_id')
                    ->where('city_id', $cityId)
                    ->where('slug', $slug)
                    ->exists()) {
                    $skipped++;

                    continue;
                }

                $location = ServiceTransferLocation::query()->create([
                    'account_id' => null,
                    'service_transfer_location_type_id' => $typeId,
                    'city_id' => $cityId,
                    'slug' => $slug,
                    'address' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'airport_code' => $row['airport_code'],
                    'parent_id' => null,
                    'is_active' => true,
                ]);

                $translatedRow = $batchTranslations[$index] ?? [];

                foreach ($languages as $language) {
                    $langId = (int) $language->getKey();
                    $code = $this->localeCodeForLanguage($langId, $languages);

                    if ($langId === $sourceLanguageId) {
                        $label = $row['name'];
                    } elseif ($translateToOtherLanguages) {
                        $label = trim((string) ($translatedRow[$code] ?? ''));
                        if ($label === '') {
                            $label = $row['name'];
                            $translationFallbacks++;
                        }
                    } else {
                        $label = $row['name'];
                    }

                    ServiceTransferLocationTranslation::query()->create([
                        'service_transfer_location_id' => (int) $location->getKey(),
                        'language_id' => $langId,
                        'name' => $label,
                    ]);
                }

                $created++;
            }
        });

        return [
            'created' => $created,
            'skipped' => $skipped,
            'removed' => $removed,
            'ai_count' => count($suggestions),
            'translation_fallbacks' => $translationFallbacks,
            'openai_calls' => $openaiCalls,
        ];
    }

    public function systemCatalogCountForCity(int $cityId): int
    {
        return ServiceTransferLocation::query()
            ->whereNull('account_id')
            ->where('city_id', $cityId)
            ->count();
    }

    private function deleteSystemCatalogForCity(int $cityId): int
    {
        $ids = ServiceTransferLocation::query()
            ->whereNull('account_id')
            ->where('city_id', $cityId)
            ->pluck('id');

        if ($ids->isEmpty()) {
            return 0;
        }

        ServiceTransferLocationTranslation::query()
            ->whereIn('service_transfer_location_id', $ids)
            ->delete();

        ServiceTransferLocation::query()->whereIn('id', $ids)->delete();

        return $ids->count();
    }

    /**
     * @param  Collection<int, Language>  $languages
     */
    private function resolveSourceLanguageId(?int $sourceLanguageId, Collection $languages): int
    {
        if ($sourceLanguageId !== null && $sourceLanguageId > 0 && $languages->contains('id', $sourceLanguageId)) {
            return $sourceLanguageId;
        }

        $spanish = $languages->first(function (Language $language): bool {
            $tag = strtolower((string) ($language->locale?->tag ?? ''));

            return str_starts_with($tag, 'es');
        });

        if ($spanish !== null) {
            return (int) $spanish->getKey();
        }

        return (int) ($languages->first()?->getKey() ?? 1);
    }

    /**
     * @param  Collection<int, Language>  $languages
     * @return Collection<int, array{code: string}>
     */
    private function targetLanguagesExcludingSource(Collection $languages, int $sourceLanguageId): Collection
    {
        return $languages
            ->filter(fn (Language $language): bool => (int) $language->getKey() !== $sourceLanguageId)
            ->map(fn (Language $language): array => [
                'code' => $this->localeCodeForLanguage((int) $language->getKey(), $languages),
            ])
            ->values();
    }

    /**
     * @param  Collection<int, Language>  $languages
     */
    private function localeCodeForLanguage(int $languageId, Collection $languages): string
    {
        $language = $languages->firstWhere('id', $languageId);
        $tag = strtolower((string) ($language?->locale?->tag ?? 'es'));

        if (str_starts_with($tag, 'pt')) {
            return 'pt';
        }
        if (str_starts_with($tag, 'en')) {
            return 'en';
        }

        return 'es';
    }

    private function slugFromDisplayName(string $name): string
    {
        $normalized = str_replace(['–', '—', '‐'], '-', $name);

        return Str::slug($normalized, '-', 'en');
    }
}
