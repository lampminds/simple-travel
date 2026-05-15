<?php

namespace App\Livewire\ServiceWizard;

use App\Models\Service;
use App\Models\ServiceActivity;
use App\Models\ServiceActivityType;
use App\Models\ServiceActivityTypeCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ServiceActivityAdvancedStep extends Component
{
    public int $serviceId;

    public int $serviceTypeId;

    /** @var list<string> */
    public array $activityTypeIds = [];

    public bool $guide_included = false;

    public bool $transport_included = false;

    public bool $outdoor_activity = false;

    public ?string $saveMessage = null;

    public function mount(int $serviceId, int $serviceTypeId): void
    {
        $this->serviceId = $serviceId;
        $this->serviceTypeId = $serviceTypeId;

        $service = $this->authorizedService();
        $row = ServiceActivity::query()
            ->where('service_id', $service->id)
            ->with('activityTypes')
            ->first();

        if ($row === null) {
            $this->activityTypeIds = [];

            return;
        }

        $this->activityTypeIds = $row->activityTypes
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();
        $this->guide_included = (bool) $row->guide_included;
        $this->transport_included = (bool) $row->transport_included;
        $this->outdoor_activity = (bool) $row->outdoor_activity;
    }

    public function selectAllTypesInCategory(int $categoryId): void
    {
        $ids = ServiceActivityType::query()
            ->where('service_activity_type_category_id', $categoryId)
            ->where('active', true)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $this->activityTypeIds = collect($this->activityTypeIds)
            ->merge($ids)
            ->unique()
            ->values()
            ->all();
    }

    public function clearTypesInCategory(int $categoryId): void
    {
        $remove = ServiceActivityType::query()
            ->where('service_activity_type_category_id', $categoryId)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $removeFlip = array_flip($remove);
        $this->activityTypeIds = collect($this->activityTypeIds)
            ->reject(fn ($id) => isset($removeFlip[(string) $id]))
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();
    }

    public function selectAllCatalogTypes(): void
    {
        $this->activityTypeIds = ServiceActivityType::query()
            ->where('active', true)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();
    }

    public function clearAllCatalogTypes(): void
    {
        $this->activityTypeIds = [];
    }

    public function save(): void
    {
        $this->saveMessage = null;

        $typeTable = (new ServiceActivityType)->getTable();
        $syncIds = collect($this->activityTypeIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        Validator::make(
            ['activityTypeIds' => $syncIds],
            [
                'activityTypeIds' => ['required', 'array', 'min:1'],
                'activityTypeIds.*' => ['required', 'integer', Rule::exists($typeTable, 'id')->where('active', true)],
            ],
            [],
            [
                'activityTypeIds' => __('wizard.step7_activity_field_types'),
                'activityTypeIds.*' => __('wizard.step7_activity_field_type'),
            ]
        )->validate();

        $service = $this->authorizedService();

        DB::transaction(function () use ($service, $syncIds): void {
            $profile = ServiceActivity::query()->updateOrCreate(
                ['service_id' => $service->id],
                [
                    'guide_included' => $this->guide_included,
                    'transport_included' => $this->transport_included,
                    'outdoor_activity' => $this->outdoor_activity,
                    'active' => true,
                ]
            );
            $profile->activityTypes()->sync($syncIds);
        });

        $this->activityTypeIds = collect($syncIds)->map(fn (int $id) => (string) $id)->values()->all();

        $this->saveMessage = __('wizard.step7_activity_saved');
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
        $categories = ServiceActivityTypeCategory::query()
            ->where('active', true)
            ->with([
                'translations.language.locale',
                'activityTypes' => fn ($q) => $q
                    ->where('active', true)
                    ->with(['translations.language.locale'])
                    ->ordered(),
            ])
            ->ordered()
            ->get()
            ->filter(fn (ServiceActivityTypeCategory $c) => $c->activityTypes->isNotEmpty())
            ->values();

        return view('livewire.service-wizard.service-activity-advanced-step', [
            'categories' => $categories,
        ]);
    }
}
