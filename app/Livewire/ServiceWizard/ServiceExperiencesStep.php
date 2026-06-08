<?php

namespace App\Livewire\ServiceWizard;

use App\Models\Service;
use App\Models\ServiceExperience;
use App\Models\ServiceType;
use App\Support\ServiceWizardStepEight;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ServiceExperiencesStep extends Component
{
    public int $serviceId;

    public int $serviceTypeId;

    /** @var array<string> */
    public array $selectedExperienceIds = [];

    public function mount(int $serviceId, int $serviceTypeId): void
    {
        $this->serviceId = $serviceId;
        $this->serviceTypeId = $serviceTypeId;

        $service = $this->authorizedService();
        $service->load('experiences');

        $this->selectedExperienceIds = $service->experiences
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();
    }

    public function selectAllExperiences(): void
    {
        $this->selectedExperienceIds = $this->catalogExperiences()
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();
    }

    public function clearAllExperiences(): void
    {
        $this->selectedExperienceIds = [];
    }

    /**
     * @return Collection<int, ServiceExperience>
     */
    protected function catalogExperiences(): Collection
    {
        return ServiceExperience::query()
            ->where('active', true)
            ->ordered()
            ->with(['translations.language.locale'])
            ->get();
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
                'service' => $service,
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
        return view('livewire.service-wizard.service-experiences-step', [
            'experiences' => $this->catalogExperiences(),
        ]);
    }
}
