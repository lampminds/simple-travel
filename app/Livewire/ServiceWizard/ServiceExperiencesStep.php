<?php

namespace App\Livewire\ServiceWizard;

use App\Models\Service;
use App\Models\ServiceExperience;
use App\Models\ServiceExperienceCategory;
use App\Models\ServiceType;
use App\Support\ServiceWizardStepEight;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ServiceExperiencesStep extends Component
{
    public int $serviceId;

    public int $serviceTypeId;

    /** @var array<string> */
    public array $categoryIds = [];

    /** @var array<string> */
    public array $selectedExperienceIds = [];

    public function mount(int $serviceId, int $serviceTypeId): void
    {
        $this->serviceId = $serviceId;
        $this->serviceTypeId = $serviceTypeId;

        $service = $this->authorizedService();
        $service->load('experiences');

        $this->categoryIds = [];
        $this->selectedExperienceIds = $service->experiences
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
        $this->categoryIds = collect(array_keys($this->categoryCheckboxOptions()))
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
     * @return array<int, string>
     */
    protected function getVisibleExperienceIdStrings(): array
    {
        $categoryIdsInt = collect($this->categoryIds)->map(fn ($id) => (int) $id)->filter(fn (int $id) => $id > 0)->unique()->values()->all();
        if ($categoryIdsInt === []) {
            return [];
        }

        return ServiceExperience::query()
            ->where('active', true)
            ->whereIn('service_experience_category_id', $categoryIdsInt)
            ->ordered()
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();
    }

    public function selectAllVisibleExperiences(): void
    {
        $this->selectedExperienceIds = $this->getVisibleExperienceIdStrings();
    }

    public function clearAllExperiences(): void
    {
        $this->selectedExperienceIds = [];
    }

    /**
     * Drop selected experiences that are not visible under the current category filter.
     * When no category is selected, keep in-memory selections (e.g. loaded from the DB) unchanged.
     */
    protected function pruneSelectionToCategories(): void
    {
        $categoryIdsInt = collect($this->categoryIds)->map(fn ($id) => (int) $id)->filter(fn (int $id) => $id > 0)->unique()->values()->all();
        if ($categoryIdsInt === []) {
            return;
        }

        $allowed = collect($this->getVisibleExperienceIdStrings())->flip();
        $this->selectedExperienceIds = collect($this->selectedExperienceIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $allowed->has((string) $id))
            ->map(fn (int $id) => (string) $id)
            ->values()
            ->all();
    }

    /**
     * @return array<string, string>
     */
    protected function categoryCheckboxOptions(): array
    {
        $categories = ServiceExperienceCategory::query()
            ->where('active', true)
            ->whereHas('experiences', fn ($q) => $q->where('active', true))
            ->ordered()
            ->with(['translations.language.locale'])
            ->get();

        $options = [];
        foreach ($categories as $category) {
            $options[(string) $category->id] = $category->name !== '' ? $category->name : (string) $category->code;
        }

        return $options;
    }

    public function save(): void
    {
        $service = $this->authorizedService();

        $requested = collect($this->selectedExperienceIds)->map(fn ($id) => (int) $id)->filter(fn (int $id) => $id > 0)->unique()->values()->all();

        $validated = validator(
            ['experience_ids' => $requested],
            [
                'experience_ids' => ['array'],
                'experience_ids.*' => ['integer', Rule::exists(ServiceExperience::class, 'id')->where('active', true)],
            ],
            [],
            ['experience_ids' => __('wizard.experiences_field_experiences')]
        )->validate();

        $final = $validated['experience_ids'] ?? [];

        DB::transaction(function () use ($service, $final): void {
            $service->experiences()->sync($final);
        });

        session()->flash('status', __('wizard.experiences_saved'));

        $serviceType = ServiceType::query()->findOrFail($this->serviceTypeId);

        if (ServiceWizardStepEight::isEnabledForServiceTypeCode($serviceType->code)) {
            $this->redirectRoute('services.wizard.step8', [
                'serviceType' => $serviceType->code,
                'service' => $service->id,
            ]);

            return;
        }

        $this->redirectRoute('catalog');
    }

    protected function authorizedService(): Service
    {
        $accountId = Auth::user()?->currentAccountId();
        abort_unless($accountId, 403);

        return Service::query()
            ->where('account_id', $accountId)
            ->where('service_type_id', $this->serviceTypeId)
            ->findOrFail($this->serviceId);
    }

    public function render(): View
    {
        $categoryIdsInt = collect($this->categoryIds)->map(fn ($id) => (int) $id)->filter(fn (int $id) => $id > 0)->unique()->values()->all();

        $experiences = $categoryIdsInt === []
            ? collect()
            : ServiceExperience::query()
                ->where('active', true)
                ->whereIn('service_experience_category_id', $categoryIdsInt)
                ->ordered()
                ->with(['translations.language.locale', 'category'])
                ->get();

        $grouped = $experiences->groupBy(fn (ServiceExperience $e) => (int) $e->service_experience_category_id);
        $orderedGrouped = collect();
        if ($grouped->isNotEmpty()) {
            $categoryOrder = ServiceExperienceCategory::query()
                ->whereIn('id', $grouped->keys()->map(fn ($k) => (int) $k)->all())
                ->ordered()
                ->pluck('id');
            foreach ($categoryOrder as $cid) {
                if ($grouped->has($cid)) {
                    $orderedGrouped->put($cid, $grouped->get($cid));
                }
            }
        }

        return view('livewire.service-wizard.service-experiences-step', [
            'categoryOptions' => $this->categoryCheckboxOptions(),
            'groupedExperiences' => $orderedGrouped,
        ]);
    }
}
