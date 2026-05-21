<?php

namespace App\Livewire\ServiceWizard;

use App\Models\Service;
use App\Models\ServiceFeature;
use App\Models\ServiceFeatureCategory;
use App\Models\ServiceType;
use App\Services\ServiceFeatureSelectionService;
use App\Support\ServiceWizardSkipsVariantsStep;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ServiceFeaturesStep extends Component
{
    public int $serviceId;

    public int $serviceTypeId;

    /** @var array<string> */
    public array $categoryIds = [];

    /** @var array<string> */
    public array $selectedFeatureIds = [];

    public function mount(int $serviceId, int $serviceTypeId): void
    {
        $this->serviceId = $serviceId;
        $this->serviceTypeId = $serviceTypeId;

        $service = $this->authorizedService();
        $catalog = app(ServiceFeatureSelectionService::class);

        $this->categoryIds = [];

        $service->load('features');
        $this->selectedFeatureIds = $service->features
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();

        $this->pruneSelectionToCategories();
    }

    public function updatedCategoryIds(): void
    {
        $this->pruneSelectionToCategories();
    }

    public function selectAllCategories(): void
    {
        $catalog = app(ServiceFeatureSelectionService::class);
        $this->categoryIds = collect(array_keys($catalog->categoryCheckboxOptionsForServiceType($this->serviceTypeId)))
            ->map(fn ($k) => (string) $k)
            ->values()
            ->all();
        $this->pruneSelectionToCategories();
    }

    public function clearAllCategories(): void
    {
        $this->categoryIds = [];
        $this->pruneSelectionToCategories();
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

    public function selectAllFeaturesInCategory(int $categoryId): void
    {
        if (! $this->categoryFilterContains($categoryId)) {
            return;
        }

        $newIds = $this->getFeatureIdStringsForCategory($categoryId);
        if ($newIds === []) {
            return;
        }

        $this->selectedFeatureIds = collect($this->selectedFeatureIds)
            ->merge($newIds)
            ->unique()
            ->values()
            ->all();
    }

    public function clearFeaturesInCategory(int $categoryId): void
    {
        if (! $this->categoryFilterContains($categoryId)) {
            return;
        }

        $inCategory = collect($this->getFeatureIdStringsForCategory($categoryId))->flip();
        if ($inCategory->isEmpty()) {
            return;
        }

        $this->selectedFeatureIds = collect($this->selectedFeatureIds)
            ->reject(fn (string $id) => $inCategory->has($id))
            ->values()
            ->all();
    }

    protected function categoryFilterContains(int $categoryId): bool
    {
        return collect($this->categoryIds)
            ->contains(fn (string|int $id) => (int) $id === $categoryId);
    }

    /**
     * Drop selected features that are no longer visible under the current category filter.
     * When no category is selected, keep in-memory selections (e.g. loaded from the DB) unchanged.
     */
    protected function pruneSelectionToCategories(): void
    {
        $catalog = app(ServiceFeatureSelectionService::class);
        $scoped = $catalog->scopedFeatureIdsForServiceType($this->serviceTypeId);
        $allowed = collect($this->categoryIds)->map(fn ($id) => (int) $id)->filter(fn (int $id) => $id > 0)->unique()->values()->all();

        if ($allowed === []) {
            return;
        }

        $valid = $catalog->selectableFeaturesInCategories($allowed, $scoped)->pluck('id')->flip();

        $this->selectedFeatureIds = collect($this->selectedFeatureIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $valid->has($id))
            ->map(fn (int $id) => (string) $id)
            ->values()
            ->all();
    }

    public function save(): void
    {
        $service = $this->authorizedService();
        $catalog = app(ServiceFeatureSelectionService::class);
        $scoped = $catalog->scopedFeatureIdsForServiceType($this->serviceTypeId);

        $categoryIds = collect($this->categoryIds)->map(fn ($id) => (int) $id)->filter(fn (int $id) => $id > 0)->unique()->values()->all();
        $requested = collect($this->selectedFeatureIds)->map(fn ($id) => (int) $id)->filter(fn (int $id) => $id > 0)->unique()->values()->all();

        if ($categoryIds === [] && $requested !== []) {
            $categoryIds = ServiceFeature::query()
                ->whereIn('id', $requested)
                ->where('active', true)
                ->where('is_selectable', true)
                ->whereIn('id', $scoped->all())
                ->pluck('service_feature_category_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();
        }

        $final = $catalog->filterFeatureIdsToCategoriesAndScope($requested, $categoryIds, $scoped);

        DB::transaction(function () use ($service, $final): void {
            $service->features()->sync($final);
        });

        session()->flash('status', __('wizard.features_saved'));

        $serviceType = ServiceType::query()->findOrFail($this->serviceTypeId);

        $nextRoute = ServiceWizardSkipsVariantsStep::isSkippedForServiceTypeCode($serviceType->code)
            ? 'services.wizard.step5'
            : 'services.wizard.step4';

        $this->redirectRoute($nextRoute, [
            'serviceType' => $serviceType->code,
            'service' => $service->id,
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

        $categoryIdsInt = collect($this->categoryIds)->map(fn ($id) => (int) $id)->filter(fn (int $id) => $id > 0)->unique()->values()->all();
        $features = $catalog->selectableFeaturesInCategories($categoryIdsInt, $scoped);

        $grouped = $features->groupBy(fn ($f) => (int) $f->service_feature_category_id);
        $orderedGrouped = collect();
        if ($grouped->isNotEmpty()) {
            $categoryOrder = ServiceFeatureCategory::query()
                ->whereIn('id', $grouped->keys()->map(fn ($k) => (int) $k)->all())
                ->ordered()
                ->pluck('id');
            foreach ($categoryOrder as $cid) {
                if ($grouped->has($cid)) {
                    $orderedGrouped->put($cid, $grouped->get($cid));
                }
            }
        }

        return view('livewire.service-wizard.service-features-step', [
            'categoryOptions' => $catalog->categoryCheckboxOptionsForServiceType($this->serviceTypeId),
            'scopedCount' => $scoped->count(),
            'groupedFeatures' => $orderedGrouped,
        ]);
    }
}
