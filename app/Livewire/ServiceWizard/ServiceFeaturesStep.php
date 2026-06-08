<?php

namespace App\Livewire\ServiceWizard;

use App\Models\Service;
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

    /** @var array<string> */
    public array $selectedFeatureIds = [];

    /** @var array<int> Category accordion panels currently expanded. */
    public array $openAccordionCategoryIds = [];

    public function mount(int $serviceId, int $serviceTypeId): void
    {
        $this->serviceId = $serviceId;
        $this->serviceTypeId = $serviceTypeId;

        $service = $this->authorizedService();

        $service->load('features');
        $this->selectedFeatureIds = $service->features
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();

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

    public function selectAllFeaturesInCategory(int $categoryId): void
    {
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
        $inCategory = collect($this->getFeatureIdStringsForCategory($categoryId))->flip();
        if ($inCategory->isEmpty()) {
            return;
        }

        $this->selectedFeatureIds = collect($this->selectedFeatureIds)
            ->reject(fn (string $id) => $inCategory->has($id))
            ->values()
            ->all();
    }

    /**
     * Drop selected features that are no longer in scope for this service type.
     */
    protected function pruneSelectionToScope(): void
    {
        $catalog = app(ServiceFeatureSelectionService::class);
        $scoped = $catalog->scopedFeatureIdsForServiceType($this->serviceTypeId);
        $valid = $scoped->flip();

        $this->selectedFeatureIds = collect($this->selectedFeatureIds)
            ->filter(fn (string $id) => $valid->has((int) $id))
            ->values()
            ->all();
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
        $requested = collect($this->selectedFeatureIds)->map(fn ($id) => (int) $id)->filter(fn (int $id) => $id > 0)->unique()->values()->all();

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
