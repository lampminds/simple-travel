<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Language;
use App\Models\OperatorPackageItem;
use App\Models\OperatorPackageItemConditionOverride;
use App\Models\OperatorServiceCatalog;
use App\Models\ServiceDetail;
use App\Models\ServiceDetailTopic;
use App\Models\ServiceOffer;
use Illuminate\Support\Collection;

/**
 * Builds inheritable condition rows and normalizes override payloads for package forms.
 */
final class OperatorPackageConditionFormService
{
    /** @var list<string> */
    private const VISIBILITIES = ['public', 'operator'];

    public function __construct(
        private readonly PackageConditionResolver $conditionResolver,
        private readonly OperatorPackageOfferCatalog $offerCatalog,
    ) {
    }

    /**
     * @return list<array{
     *     topic_id: int,
     *     topic_code: string,
     *     topic_name: string,
     *     category_name: string,
     *     inherited_by_language: array<int, string>,
     *     operator_override_mode: string,
     *     allowed_actions: list<string>,
     *     can_customize: bool,
     *     saved_action: string|null,
     *     saved_translations: array<int, string>
     * }>
     */
    public function inheritableRowsForOffer(
        int $operatorAccountId,
        int $offerId,
        ?OperatorPackageItem $existingItem = null,
    ): array {
        $offer = $this->offerCatalog->findEligibleOffer($operatorAccountId, $offerId);
        if ($offer === null) {
            return [];
        }

        $offer->loadMissing([
            'serviceVariant.service.serviceDetails.serviceDetailTopic.category.translations.language.locale',
            'serviceVariant.service.serviceDetails.serviceDetailTopic.translations.language.locale',
            'serviceVariant.service.serviceDetails.language.locale',
        ]);

        $service = $offer->serviceVariant?->service;
        if ($service === null) {
            return [];
        }

        $savedByTopic = $this->savedOverridesByTopic($existingItem);
        $grouped = $this->groupServiceDetails($service->serviceDetails);
        $rows = [];

        foreach ($grouped as $topicId => $details) {
            /** @var ServiceDetail $first */
            $first = $details->first();
            $topic = $first->serviceDetailTopic;
            if ($topic === null) {
                continue;
            }

            $inheritedByLanguage = $this->textsByLanguage($details);
            if ($inheritedByLanguage === [] && ! isset($savedByTopic[$topicId])) {
                continue;
            }

            $allowedActions = $this->conditionResolver->allowedActionsForTopic($topic);
            $saved = $savedByTopic[$topicId] ?? null;

            $rows[] = [
                'topic_id' => $topicId,
                'topic_code' => (string) $topic->code,
                'topic_name' => trim((string) $topic->name) ?: '—',
                'category_name' => trim((string) ($topic->category?->name ?? '')) ?: '—',
                'inherited_by_language' => $inheritedByLanguage,
                'operator_override_mode' => $this->effectiveOverrideModeLabel($topic),
                'allowed_actions' => $allowedActions,
                'can_customize' => $allowedActions !== [],
                'saved_action' => $saved['action'] ?? null,
                'saved_translations' => $saved['translations'] ?? [],
            ];
        }

        usort($rows, fn (array $a, array $b): int => ($a['topic_name'] ?? '') <=> ($b['topic_name'] ?? ''));

        return $rows;
    }

    /**
     * @return list<array{topic_id: int, label: string, operator_override_mode: string, allowed_actions: list<string>}>
     */
    public function packageLevelTopicOptions(): array
    {
        $topics = ServiceDetailTopic::query()
            ->where('active', true)
            ->with([
                'category.translations.language.locale',
                'translations.language.locale',
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $options = [];
        foreach ($topics as $topic) {
            $allowedActions = $this->conditionResolver->allowedActionsForTopic($topic);
            if ($allowedActions === []) {
                continue;
            }

            $category = trim((string) ($topic->category?->name ?? '')) ?: '—';
            $name = trim((string) $topic->name) ?: (string) $topic->code;

            $options[] = [
                'topic_id' => (int) $topic->id,
                'label' => $category.' · '.$name,
                'operator_override_mode' => $this->effectiveOverrideModeLabel($topic),
                'allowed_actions' => $allowedActions,
            ];
        }

        return $options;
    }

    /**
     * @return list<array{
     *     service_detail_topic_id: int,
     *     action: string,
     *     translations: array<int, string>
     * }>
     */
    public function packageOverridesForForm(OperatorServiceCatalog $package): array
    {
        $package->loadMissing([
            'conditionOverrides.translations',
            'conditionOverrides.serviceDetailTopic',
        ]);

        $rows = [];
        foreach ($package->conditionOverrides as $override) {
            $rows[] = [
                'service_detail_topic_id' => (int) $override->service_detail_topic_id,
                'action' => (string) $override->action,
                'translations' => $this->translationsMap($override->translations),
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array{action: string, translations: array<int, string>}>
     */
    private function savedOverridesByTopic(?OperatorPackageItem $item): array
    {
        if ($item === null) {
            return [];
        }

        $item->loadMissing(['conditionOverrides.translations']);

        $map = [];
        foreach ($item->conditionOverrides as $override) {
            $map[(int) $override->service_detail_topic_id] = [
                'action' => (string) $override->action,
                'translations' => $this->translationsMap($override->translations),
            ];
        }

        return $map;
    }

    /**
     * @param  Collection<int, ServiceDetail>  $serviceDetails
     * @return array<int, Collection<int, ServiceDetail>>
     */
    private function groupServiceDetails(Collection $serviceDetails): array
    {
        $grouped = [];

        foreach ($serviceDetails as $detail) {
            if (! $detail->active) {
                continue;
            }

            if (! in_array((string) ($detail->serviceDetailTopic?->visibility ?? 'public'), self::VISIBILITIES, true)) {
                continue;
            }

            $topicId = (int) $detail->service_detail_topic_id;
            if (! isset($grouped[$topicId])) {
                $grouped[$topicId] = collect();
            }
            $grouped[$topicId]->push($detail);
        }

        return $grouped;
    }

    /**
     * @param  Collection<int, ServiceDetail>  $details
     * @return array<int, string>
     */
    private function textsByLanguage(Collection $details): array
    {
        $map = [];
        foreach ($details as $detail) {
            $text = trim((string) ($detail->description ?? ''));
            if ($text === '') {
                continue;
            }
            $map[(int) $detail->language_id] = $text;
        }

        return $map;
    }

    /**
     * @param  iterable<int, \Illuminate\Database\Eloquent\Model>  $translations
     * @return array<int, string>
     */
    private function translationsMap(iterable $translations): array
    {
        $map = [];
        foreach ($translations as $translation) {
            $text = trim((string) ($translation->custom_text ?? ''));
            if ($text === '') {
                continue;
            }
            $map[(int) $translation->language_id] = $text;
        }

        return $map;
    }

    private function effectiveOverrideModeLabel(ServiceDetailTopic $topic): string
    {
        $topicMode = trim((string) ($topic->operator_override_mode ?? ''));
        if ($topicMode !== '' && $topicMode !== 'none') {
            return $topicMode;
        }

        $categoryMode = trim((string) ($topic->category?->operator_override_mode ?? ''));

        return $categoryMode !== '' ? $categoryMode : 'none';
    }

    /**
     * @param  list<string>  $allowedActions
     */
    public function actionRequiresText(string $action): bool
    {
        return in_array($action, [
            OperatorPackageItemConditionOverride::ACTION_APPEND_TOP,
            OperatorPackageItemConditionOverride::ACTION_APPEND_BOTTOM,
            OperatorPackageItemConditionOverride::ACTION_REPLACE,
        ], true);
    }

    /**
     * @param  array<int, string>  $translations
     */
    public function hasAnyCustomText(array $translations): bool
    {
        foreach ($translations as $text) {
            if (trim((string) $text) !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection<int, Language>
     */
    public function languages(): Collection
    {
        return Language::query()->with('locale')->orderBy('id')->get();
    }
}
