<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Locale;
use App\Models\OperatorPackageConditionOverride;
use App\Models\OperatorPackageItem;
use App\Models\OperatorPackageItemConditionOverride;
use App\Models\OperatorServiceCatalog;
use App\Models\ServiceDetail;
use App\Models\ServiceDetailConditionKey;
use App\Models\ServiceDetailTopic;
use App\Models\ServiceDetailTopicCategory;
use Illuminate\Support\Collection;

/**
 * Resolves inherited provider conditions with operator overrides at item and package level.
 */
final class PackageConditionResolver
{
    /** @var list<string> */
    private const VISIBILITIES_FOR_PACKAGE = ['public', 'operator'];

    /**
     * @return list<array{
     *     topic_id: int,
     *     topic_code: string,
     *     topic_name: string,
     *     category_name: string,
     *     condition_key_id: int|null,
     *     condition_key_code: string|null,
     *     consolidation_mode: string|null,
     *     visibility: string,
     *     scope: string,
     *     is_mandatory: bool,
     *     sort_order: int,
     *     inherited_text: string,
     *     effective_text: string,
     *     action: string|null,
     *     source: string,
     *     operator_override_mode: string,
     *     package_item_id: int
     * }>
     */
    public function resolveForItem(OperatorPackageItem $item, ?int $languageId = null): array
    {
        $item->loadMissing([
            'conditionOverrides.translations.language.locale',
            'conditionOverrides.serviceDetailTopic.category.translations.language.locale',
            'conditionOverrides.serviceDetailTopic.translations.language.locale',
            'serviceVariant.service.serviceDetails.serviceDetailTopic.category.translations.language.locale',
            'serviceVariant.service.serviceDetails.serviceDetailTopic.translations.language.locale',
            'serviceVariant.service.serviceDetails.language.locale',
            'serviceVariant.service.serviceDetails.conditionKey',
        ]);

        $service = $item->serviceVariant?->service;
        if ($service === null) {
            return [];
        }

        $locale = app()->getLocale();
        $overridesByTopic = $item->conditionOverrides->keyBy('service_detail_topic_id');

        return $this->resolveInheritedGroups(
            $service->serviceDetails,
            $overridesByTopic,
            (int) $item->id,
            'item',
            $languageId,
            $locale,
        );
    }

    /**
     * @return array{
     *     by_item: array<int, list<array<string, mixed>>>,
     *     package_level: list<array<string, mixed>>,
     *     consolidated_by_condition_key: list<array<string, mixed>>,
     *     consolidated_by_topic: list<array<string, mixed>>
     * }
     */
    public function resolveForPackage(OperatorServiceCatalog $package, ?int $languageId = null): array
    {
        $package->loadMissing([
            'items.conditionOverrides.translations.language.locale',
            'items.conditionOverrides.serviceDetailTopic.category.translations.language.locale',
            'items.conditionOverrides.serviceDetailTopic.translations.language.locale',
            'items.serviceOffer.providerAccount',
            'items.serviceVariant.service.serviceDetails.serviceDetailTopic.category.translations.language.locale',
            'items.serviceVariant.service.serviceDetails.serviceDetailTopic.translations.language.locale',
            'items.serviceVariant.service.serviceDetails.language.locale',
            'items.serviceVariant.service.serviceDetails.conditionKey',
            'conditionOverrides.translations.language.locale',
            'conditionOverrides.serviceDetailTopic.category.translations.language.locale',
            'conditionOverrides.serviceDetailTopic.translations.language.locale',
            'conditionOverrides.serviceDetailTopic.conditionKey',
        ]);

        $locale = app()->getLocale();
        $byItem = [];
        $allItemConditions = [];

        foreach ($package->items->sortBy('sort_order') as $item) {
            $conditions = $this->resolveForItem($item, $languageId);
            $byItem[(int) $item->id] = $conditions;
            foreach ($conditions as $condition) {
                $allItemConditions[] = array_merge($condition, [
                    'provider_label' => $this->providerLabel($item),
                ]);
            }
        }

        $consolidatedByTopic = $this->consolidateByTopic($allItemConditions, $package, $languageId, $locale);
        $consolidatedByConditionKey = $this->consolidateByConditionKey($allItemConditions, $consolidatedByTopic);
        $packageLevel = $this->resolvePackageLevelOnly($package, $allItemConditions, $languageId, $locale);

        return [
            'by_item' => $byItem,
            'package_level' => $packageLevel,
            'consolidated_by_condition_key' => $consolidatedByConditionKey,
            'consolidated_by_topic' => $consolidatedByTopic,
        ];
    }

    public function isActionAllowedForTopic(string $action, ServiceDetailTopic $topic): bool
    {
        return in_array($action, $this->allowedActionsForTopic($topic), true);
    }

    /**
     * @return list<string>
     */
    public function allowedActionsForTopic(ServiceDetailTopic $topic): array
    {
        return match ($this->effectiveOverrideMode($topic)) {
            'append_only' => [
                OperatorPackageItemConditionOverride::ACTION_APPEND_TOP,
                OperatorPackageItemConditionOverride::ACTION_APPEND_BOTTOM,
            ],
            'replace' => [OperatorPackageItemConditionOverride::ACTION_REPLACE],
            'suppress' => [OperatorPackageItemConditionOverride::ACTION_SUPPRESS],
            default => [],
        };
    }

    /**
     * @param  Collection<int, ServiceDetail>  $serviceDetails
     * @param  Collection<int, OperatorPackageItemConditionOverride|OperatorPackageConditionOverride>  $overridesByTopic
     * @return list<array<string, mixed>>
     */
    private function resolveInheritedGroups(
        Collection $serviceDetails,
        Collection $overridesByTopic,
        int $packageItemId,
        string $sourcePrefix,
        ?int $languageId,
        string $locale,
    ): array {
        $grouped = $serviceDetails
            ->filter(fn (ServiceDetail $detail) => in_array(
                (string) ($detail->serviceDetailTopic?->visibility ?? 'public'),
                self::VISIBILITIES_FOR_PACKAGE,
                true,
            ))
            ->groupBy(fn (ServiceDetail $detail) => implode('|', [
                (int) $detail->service_detail_topic_id,
                (int) ($detail->condition_key_id ?? 0),
                (int) $detail->sort_order,
            ]));

        $rows = [];

        foreach ($grouped as $details) {
            /** @var ServiceDetail $first */
            $first = $details->first();
            if (! $first->active) {
                continue;
            }

            $topic = $first->serviceDetailTopic;
            if ($topic === null) {
                continue;
            }

            $inheritedText = $this->textForLanguage($details, $languageId, $locale, 'description');
            $override = $overridesByTopic->get((int) $topic->id);
            $action = $override?->action;
            $customText = $override !== null
                ? $this->textForLanguage($override->translations, $languageId, $locale, 'custom_text')
                : '';

            $effectiveText = $this->applyAction($inheritedText, $action, $customText);
            if ($effectiveText === '' && $inheritedText === '' && $override === null) {
                continue;
            }

            $conditionKey = $first->conditionKey ?? $topic->conditionKey;

            $rows[] = [
                'topic_id' => (int) $topic->id,
                'topic_code' => (string) $topic->code,
                'topic_name' => $this->topicDisplayLabel($topic),
                'category_name' => $this->categoryDisplayLabel($topic->category),
                'condition_key_id' => $conditionKey?->id !== null ? (int) $conditionKey->id : null,
                'condition_key_code' => $conditionKey !== null ? (string) $conditionKey->full_code : null,
                'consolidation_mode' => $conditionKey?->consolidation_mode,
                'visibility' => (string) ($topic->visibility ?? 'public'),
                'scope' => (string) ($topic->scope ?? 'informational'),
                'is_mandatory' => (bool) $first->is_mandatory,
                'sort_order' => (int) $first->sort_order,
                'inherited_text' => $inheritedText,
                'effective_text' => $effectiveText,
                'action' => $action,
                'source' => $override !== null ? $sourcePrefix.'_override' : $sourcePrefix.'_inherited',
                'operator_override_mode' => $this->effectiveOverrideMode($topic),
                'package_item_id' => $packageItemId,
            ];
        }

        usort($rows, fn (array $a, array $b): int => [$a['sort_order'], $a['topic_name']] <=> [$b['sort_order'], $b['topic_name']]);

        return $rows;
    }

    /**
     * @param  list<array<string, mixed>>  $allItemConditions
     * @return list<array<string, mixed>>
     */
    private function consolidateByTopic(
        array $allItemConditions,
        OperatorServiceCatalog $package,
        ?int $languageId,
        string $locale,
    ): array {
        $byTopic = collect($allItemConditions)->groupBy('topic_id');
        $packageOverrides = $package->conditionOverrides->keyBy('service_detail_topic_id');
        $rows = [];

        foreach ($byTopic as $topicId => $conditions) {
            /** @var Collection<int, array<string, mixed>> $conditions */
            $first = $conditions->first();
            if (! is_array($first)) {
                continue;
            }

            $aggregateInherited = $conditions
                ->pluck('effective_text')
                ->map(fn ($text) => trim((string) $text))
                ->filter(fn (string $text) => $text !== '')
                ->unique()
                ->values()
                ->implode("\n\n");

            $override = $packageOverrides->get((int) $topicId);
            $action = $override?->action;
            $customText = $override !== null
                ? $this->textForLanguage($override->translations, $languageId, $locale, 'custom_text')
                : '';

            $effectiveText = $this->applyAction($aggregateInherited, $action, $customText);
            if ($effectiveText === '' && $aggregateInherited === '' && $override === null) {
                continue;
            }

            $rows[] = [
                'topic_id' => (int) $topicId,
                'topic_code' => (string) ($first['topic_code'] ?? ''),
                'topic_name' => (string) ($first['topic_name'] ?? '—'),
                'category_name' => (string) ($first['category_name'] ?? '—'),
                'condition_key_id' => $first['condition_key_id'] ?? null,
                'condition_key_code' => $first['condition_key_code'] ?? null,
                'consolidation_mode' => $first['consolidation_mode'] ?? null,
                'visibility' => (string) ($first['visibility'] ?? 'public'),
                'scope' => (string) ($first['scope'] ?? 'informational'),
                'is_mandatory' => (bool) ($first['is_mandatory'] ?? false),
                'sort_order' => (int) ($first['sort_order'] ?? 9999),
                'inherited_text' => $aggregateInherited,
                'effective_text' => $effectiveText,
                'action' => $action,
                'source' => $override !== null ? 'package_override' : 'package_topic_aggregate',
                'operator_override_mode' => (string) ($first['operator_override_mode'] ?? 'none'),
                'contributing_item_ids' => $conditions->pluck('package_item_id')->unique()->values()->all(),
            ];
        }

        usort($rows, fn (array $a, array $b): int => [$a['sort_order'], $a['topic_name']] <=> [$b['sort_order'], $b['topic_name']]);

        return $rows;
    }

    /**
     * @param  list<array<string, mixed>>  $allItemConditions
     * @param  list<array<string, mixed>>  $consolidatedByTopic
     * @return list<array<string, mixed>>
     */
    private function consolidateByConditionKey(array $allItemConditions, array $consolidatedByTopic): array
    {
        $topicById = collect($consolidatedByTopic)->keyBy('topic_id');
        $keyed = collect($allItemConditions)
            ->filter(fn (array $row) => ($row['condition_key_id'] ?? null) !== null)
            ->groupBy('condition_key_id');

        $rows = [];

        foreach ($keyed as $conditionKeyId => $conditions) {
            /** @var Collection<int, array<string, mixed>> $conditions */
            $first = $conditions->first();
            if (! is_array($first)) {
                continue;
            }

            $mode = (string) ($first['consolidation_mode'] ?? ServiceDetailConditionKey::CONSOLIDATION_DEDUPLICATE);
            $topicIds = $conditions->pluck('topic_id')->unique()->values();
            $topicRows = $topicIds
                ->map(fn (int $topicId) => $topicById->get($topicId))
                ->filter()
                ->values();

            $texts = $topicRows
                ->pluck('effective_text')
                ->map(fn ($text) => trim((string) $text))
                ->filter(fn (string $text) => $text !== '')
                ->values();

            $effectiveText = $this->consolidateTexts($texts->all(), $mode);
            $hasConflict = $texts->unique()->count() > 1;

            if ($effectiveText === '') {
                continue;
            }

            $rows[] = [
                'condition_key_id' => (int) $conditionKeyId,
                'condition_key_code' => (string) ($first['condition_key_code'] ?? ''),
                'consolidation_mode' => $mode,
                'effective_text' => $effectiveText,
                'has_conflict' => $mode === ServiceDetailConditionKey::CONSOLIDATION_CONFLICT_CHECK && $hasConflict,
                'contributing_topic_ids' => $topicIds->all(),
                'contributing_item_ids' => $conditions->pluck('package_item_id')->unique()->values()->all(),
                'entries' => $mode === ServiceDetailConditionKey::CONSOLIDATION_SHOW_ALL
                    ? $conditions->map(fn (array $row) => [
                        'topic_id' => (int) $row['topic_id'],
                        'topic_name' => (string) ($row['topic_name'] ?? '—'),
                        'package_item_id' => (int) $row['package_item_id'],
                        'provider_label' => (string) ($row['provider_label'] ?? ''),
                        'effective_text' => (string) ($row['effective_text'] ?? ''),
                    ])->values()->all()
                    : [],
            ];
        }

        usort($rows, fn (array $a, array $b): int => ($a['condition_key_code'] ?? '') <=> ($b['condition_key_code'] ?? ''));

        return $rows;
    }

    /**
     * Package overrides for topics that do not appear on any catalog item.
     *
     * @param  list<array<string, mixed>>  $allItemConditions
     * @return list<array<string, mixed>>
     */
    private function resolvePackageLevelOnly(
        OperatorServiceCatalog $package,
        array $allItemConditions,
        ?int $languageId,
        string $locale,
    ): array {
        $topicIdsInItems = collect($allItemConditions)->pluck('topic_id')->unique();
        $rows = [];

        foreach ($package->conditionOverrides as $override) {
            if ($topicIdsInItems->contains((int) $override->service_detail_topic_id)) {
                continue;
            }

            $topic = $override->serviceDetailTopic;
            if ($topic === null) {
                continue;
            }

            $customText = $this->textForLanguage($override->translations, $languageId, $locale, 'custom_text');
            $effectiveText = $this->applyAction('', $override->action, $customText);
            if ($effectiveText === '') {
                continue;
            }

            $conditionKey = $topic->conditionKey;

            $rows[] = [
                'topic_id' => (int) $topic->id,
                'topic_code' => (string) $topic->code,
                'topic_name' => $this->topicDisplayLabel($topic),
                'category_name' => $this->categoryDisplayLabel($topic->category),
                'condition_key_id' => $conditionKey?->id !== null ? (int) $conditionKey->id : null,
                'condition_key_code' => $conditionKey !== null ? (string) $conditionKey->full_code : null,
                'consolidation_mode' => $conditionKey?->consolidation_mode,
                'visibility' => (string) ($topic->visibility ?? 'public'),
                'scope' => (string) ($topic->scope ?? 'informational'),
                'inherited_text' => '',
                'effective_text' => $effectiveText,
                'action' => $override->action,
                'source' => 'package_override',
                'operator_override_mode' => $this->effectiveOverrideMode($topic),
            ];
        }

        return $rows;
    }

    private function effectiveOverrideMode(ServiceDetailTopic $topic): string
    {
        $topicMode = trim((string) ($topic->operator_override_mode ?? ''));
        if ($topicMode !== '' && $topicMode !== 'none') {
            return $topicMode;
        }

        $categoryMode = trim((string) ($topic->category?->operator_override_mode ?? ''));

        return $categoryMode !== '' ? $categoryMode : 'none';
    }

    private function applyAction(string $inheritedText, ?string $action, string $customText): string
    {
        $inheritedText = trim($inheritedText);
        $customText = trim($customText);

        return match ($action) {
            OperatorPackageItemConditionOverride::ACTION_SUPPRESS => '',
            OperatorPackageItemConditionOverride::ACTION_REPLACE => $customText,
            OperatorPackageItemConditionOverride::ACTION_APPEND_TOP => $this->joinTexts($customText, $inheritedText),
            OperatorPackageItemConditionOverride::ACTION_APPEND_BOTTOM => $this->joinTexts($inheritedText, $customText),
            default => $inheritedText,
        };
    }

    /**
     * @param  list<string>  $texts
     */
    private function consolidateTexts(array $texts, string $mode): string
    {
        $texts = array_values(array_filter(array_map('trim', $texts), fn (string $text) => $text !== ''));
        if ($texts === []) {
            return '';
        }

        return match ($mode) {
            ServiceDetailConditionKey::CONSOLIDATION_SHOW_ALL => implode("\n\n", array_unique($texts)),
            ServiceDetailConditionKey::CONSOLIDATION_MOST_RESTRICTIVE => $texts[0],
            default => implode("\n\n", array_unique($texts)),
        };
    }

    private function joinTexts(string $first, string $second): string
    {
        if ($first === '') {
            return $second;
        }
        if ($second === '') {
            return $first;
        }

        return $first."\n\n".$second;
    }

    /**
     * @param  iterable<int, ServiceDetail|OperatorPackageConditionOverrideTranslation|OperatorPackageItemConditionOverrideTranslation>  $rows
     */
    private function textForLanguage(iterable $rows, ?int $languageId, string $locale, string $field): string
    {
        $fallback = '';

        foreach ($rows as $row) {
            $text = trim((string) ($row->{$field} ?? ''));
            if ($text === '') {
                continue;
            }

            if ($languageId !== null && (int) ($row->language_id ?? 0) === $languageId) {
                return $text;
            }

            $detailLocale = $row->language?->locale;
            if ($detailLocale !== null && Locale::primaryTagMatches($detailLocale, $locale)) {
                return $text;
            }

            if ($fallback === '') {
                $fallback = $text;
            }
        }

        return $fallback;
    }

    private function providerLabel(OperatorPackageItem $item): string
    {
        $offer = $item->serviceOffer;
        $account = $offer?->providerAccount;

        return trim((string) ($account?->commercial_name
            ?? $account?->name
            ?? ($offer !== null ? '#'.$offer->provider_id : '')));
    }

    private function topicDisplayLabel(ServiceDetailTopic $topic): string
    {
        $name = trim((string) $topic->name);
        if ($name !== '' && ! $this->looksLikeQualifiedCode($name)) {
            return $name;
        }

        return $this->humanizeCatalogCode((string) $topic->code);
    }

    private function categoryDisplayLabel(?ServiceDetailTopicCategory $category): string
    {
        if ($category === null) {
            return '—';
        }

        $name = trim((string) $category->name);
        if ($name !== '' && ! $this->looksLikeQualifiedCode($name)) {
            return $name;
        }

        $code = trim((string) ($category->code ?? ''));

        return $code !== '' ? $this->humanizeCatalogCode($code) : '—';
    }

    private function looksLikeQualifiedCode(string $value): bool
    {
        return (bool) preg_match('/^[a-z][a-z0-9_]*\.[a-z][a-z0-9_]*$/', $value);
    }

    private function humanizeCatalogCode(string $code): string
    {
        $code = trim($code);
        if ($code === '') {
            return '—';
        }

        $segment = str_contains($code, '.') ? (string) strrchr($code, '.') : $code;
        $segment = ltrim($segment, '.');

        return ucfirst(str_replace('_', ' ', $segment));
    }
}
