<?php

namespace App\Livewire\ServiceWizard;

use App\Models\Service;
use App\Models\ServiceFeature;
use App\Models\ServiceFeatureCategory;
use App\Models\ServiceType;
use App\Services\ServiceFeatureSelectionService;
use App\Support\ServiceWizardSkipsVariantsStep;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ServiceFeaturesStep extends Component
{
    public int $serviceId;

    public int $serviceTypeId;

    public const INCLUSION_UNSET = 'unset';

    /**
     * Feature id => unset|included|excluded. Unset is not stored.
     *
     * @var array<string, string>
     */
    public array $featureInclusions = [];

    /** @var array<int> Category accordion panels currently expanded. */
    public array $openAccordionCategoryIds = [];

    public function mount(int $serviceId, int $serviceTypeId): void
    {
        $this->serviceId = $serviceId;
        $this->serviceTypeId = $serviceTypeId;

        $service = $this->authorizedService();

        $service->load('features');
        $this->featureInclusions = [];
        foreach ($service->features as $feature) {
            $this->featureInclusions[(string) $feature->id] = ServiceFeature::normalizeInclusion(
                $feature->pivot->inclusion ?? null
            );
        }

        $this->pruneSelectionToScope();
    }

    public function toggleAccordion(int $categoryId): void
    {
        if ($categoryId < 1) {
            return;
        }

        if (in_array($categoryId, $this->openAccordionCategoryIds, true)) {
            $this->openAccordionCategoryIds = array_values(array_filter(
                $this->openAccordionCategoryIds,
                fn (int $id) => $id !== $categoryId
            ));
        } else {
            $this->openAccordionCategoryIds[] = $categoryId;
        }
    }

    /**
     * Selectable feature IDs for one category (scope + type), as strings for checkboxes.
     *
     * @return array<int, string>
     */
    protected function getFeatureIdStringsForCategory(int $categoryId): array
    {
        if ($categoryId < 1) {
            return [];
        }

        $catalog = app(ServiceFeatureSelectionService::class);
        $scoped = $catalog->scopedFeatureIdsForServiceType($this->serviceTypeId);

        return $catalog->selectableFeaturesInCategories([$categoryId], $scoped)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();
    }

    public function includeAllFeaturesInCategory(int $categoryId): void
    {
        foreach ($this->getFeatureIdStringsForCategory($categoryId) as $id) {
            $this->featureInclusions[$id] = ServiceFeature::INCLUSION_INCLUDED;
        }
    }

    public function clearFeaturesInCategory(int $categoryId): void
    {
        foreach ($this->getFeatureIdStringsForCategory($categoryId) as $id) {
            $this->featureInclusions[$id] = self::INCLUSION_UNSET;
        }
    }

    /**
     * Keep one state per in-scope feature. Saved rows outside the scope are dropped.
     */
    protected function pruneSelectionToScope(): void
    {
        $catalog = app(ServiceFeatureSelectionService::class);
        $scoped = $catalog->scopedFeatureIdsForServiceType($this->serviceTypeId);
        $next = [];

        foreach ($scoped as $id) {
            $key = (string) (int) $id;
            $current = $this->featureInclusions[$key] ?? self::INCLUSION_UNSET;
            $next[$key] = in_array($current, [
                self::INCLUSION_UNSET,
                ServiceFeature::INCLUSION_INCLUDED,
                ServiceFeature::INCLUSION_EXCLUDED,
            ], true) ? $current : self::INCLUSION_UNSET;
        }

        $this->featureInclusions = $next;
    }

    /**
     * @return array<int>
     */
    protected function scopedCategoryIdsForServiceType(): array
    {
        $catalog = app(ServiceFeatureSelectionService::class);

        return collect(array_keys($catalog->categoryCheckboxOptionsForServiceType($this->serviceTypeId)))
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Features grouped by category id, ordered by category list_order.
     *
     * @return Collection<int, Collection<int, ServiceFeature>>
     */
    protected function groupedFeaturesForServiceType(): Collection
    {
        $catalog = app(ServiceFeatureSelectionService::class);
        $scoped = $catalog->scopedFeatureIdsForServiceType($this->serviceTypeId);
        $categoryIds = $this->scopedCategoryIdsForServiceType();

        $features = $catalog->selectableFeaturesInCategories($categoryIds, $scoped);
        $grouped = $features->groupBy(fn ($f) => (int) $f->service_feature_category_id);
        $orderedGrouped = collect();

        if ($grouped->isEmpty()) {
            return $orderedGrouped;
        }

        $categoryOrder = ServiceFeatureCategory::query()
            ->whereIn('id', $grouped->keys()->map(fn ($k) => (int) $k)->all())
            ->ordered()
            ->pluck('id');

        foreach ($categoryOrder as $cid) {
            if ($grouped->has($cid)) {
                $orderedGrouped->put($cid, $grouped->get($cid));
            }
        }

        return $orderedGrouped;
    }

    public function save(): void
    {
        $service = $this->authorizedService();
        $catalog = app(ServiceFeatureSelectionService::class);
        $scoped = $catalog->scopedFeatureIdsForServiceType($this->serviceTypeId);

        $categoryIds = $this->scopedCategoryIdsForServiceType();
        $requested = [];
        foreach ($this->featureInclusions as $id => $inclusion) {
            $featureId = (int) $id;
            $inclusion = (string) $inclusion;
            if ($featureId < 1 || $inclusion === self::INCLUSION_UNSET) {
                continue;
            }

            $requested[$featureId] = $inclusion;
        }

        $allowedIds = array_flip($catalog->filterFeatureIdsToCategoriesAndScope(
            array_keys($requested),
            $categoryIds,
            $scoped,
        ));

        $sync = [];
        foreach ($requested as $id => $inclusion) {
            if (! isset($allowedIds[$id])) {
                continue;
            }

            $sync[$id] = [
                'inclusion' => ServiceFeature::normalizeInclusion($inclusion),
            ];
        }

        DB::transaction(function () use ($service, $sync): void {
            $service->features()->sync($sync);
        });

        session()->flash('status', __('wizard.features_saved'));

        $serviceType = ServiceType::query()->findOrFail($this->serviceTypeId);

        $nextRoute = ServiceWizardSkipsVariantsStep::isSkippedForServiceTypeCode($serviceType->code)
            ? 'services.wizard.step5'
            : 'services.wizard.step4';

        $this->redirectRoute($nextRoute, [
            'serviceType' => $serviceType->code,
            'service' => $service,
        ]);
    }

    protected function authorizedService(): Service
    {
        $accountId = Auth::user()?->currentAccountId();
        abort_unless($accountId, 403);

        $service = Service::query()
            ->where('account_id', $accountId)
            ->where('service_type_id', $this->serviceTypeId)
            ->findOrFail($this->serviceId);

        return $service;
    }

    public function render(): View
    {
        $catalog = app(ServiceFeatureSelectionService::class);
        $scoped = $catalog->scopedFeatureIdsForServiceType($this->serviceTypeId);

        return view('livewire.service-wizard.service-features-step', [
            'scopedCount' => $scoped->count(),
            'groupedFeatures' => $this->groupedFeaturesForServiceType(),
        ]);
    }
}
