<?php

namespace App\Services;

use App\Models\ServiceFeature;
use App\Models\ServiceFeatureCategory;
use App\Models\ServiceFeatureScope;
use Illuminate\Support\Collection;

/**
 * Resolves which catalogue features are allowed for a service type (admin scope)
 * and formats options for selection UIs (wizard / Filament-style flows).
 */
class ServiceFeatureSelectionService
{
    /**
     * Feature IDs that the service type may use (from cat_service_feature_scopes).
     *
     * @return Collection<int, int>
     */
    public function scopedFeatureIdsForServiceType(int $serviceTypeId): Collection
    {
        return ServiceFeatureScope::query()
            ->where('service_type_id', $serviceTypeId)
            ->pluck('service_feature_id')
            ->map(fn ($id) => (int) $id)
            ->values();
    }

    /**
     * Selectable features: in scope, active, selectable, limited to category ids.
     *
     * @param  array<int>  $categoryIds
     * @param  Collection<int, int>  $scopedFeatureIds
     * @return Collection<int, ServiceFeature>
     */
    public function selectableFeaturesInCategories(array $categoryIds, Collection $scopedFeatureIds): Collection
    {
        if ($scopedFeatureIds->isEmpty() || $categoryIds === []) {
            return collect();
        }

        return ServiceFeature::query()
            ->where('active', true)
            ->where('is_selectable', true)
            ->whereIn('id', $scopedFeatureIds->all())
            ->whereIn('service_feature_category_id', $categoryIds)
            ->with(['serviceFeatureCategory.translations.language.locale', 'translations.language.locale'])
            ->ordered()
            ->get();
    }

    /**
     * @return array<string, string> id => label
     */
    public function categoryCheckboxOptions(): array
    {
        return ServiceFeatureCategory::query()
            ->where('active', true)
            ->with(['translations.language.locale'])
            ->ordered()
            ->get()
            ->mapWithKeys(fn (ServiceFeatureCategory $c) => [
                (string) $c->id => $c->name !== '' ? $c->name : $c->code,
            ])
            ->all();
    }

    /**
     * @return array<int>
     */
    public function allActiveCategoryIds(): array
    {
        return ServiceFeatureCategory::query()
            ->where('active', true)
            ->ordered()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    public function featureOptionLabel(ServiceFeature $feature): string
    {
        $cat = $feature->serviceFeatureCategory?->name ?? '';
        $name = $feature->name !== '' ? $feature->name : $feature->code;

        return $cat !== '' ? "{$cat} — {$name}" : $name;
    }

    /**
     * Keeps only IDs that are still valid for the given categories and scope.
     *
     * @param  array<int>  $featureIds
     * @param  array<int>  $categoryIds
     * @param  Collection<int, int>  $scopedFeatureIds
     * @return array<int>
     */
    public function filterFeatureIdsToCategoriesAndScope(
        array $featureIds,
        array $categoryIds,
        Collection $scopedFeatureIds
    ): array {
        if ($featureIds === [] || $categoryIds === [] || $scopedFeatureIds->isEmpty()) {
            return [];
        }

        return ServiceFeature::query()
            ->whereIn('id', $featureIds)
            ->where('is_selectable', true)
            ->whereIn('service_feature_category_id', $categoryIds)
            ->whereIn('id', $scopedFeatureIds->all())
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }
}
