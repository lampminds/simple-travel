<?php

namespace App\Livewire\ServiceWizard;

use App\Models\Service;
use App\Models\ServiceHotel;
use App\Models\ServiceHotelType;
use App\Models\ServiceHotelTypeCategory;
use App\Support\ServiceWizardStepEight;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ServiceHotelAdvancedStep extends Component
{
    public int $serviceId;

    public int $serviceTypeId;

    /** @var list<string> Selected catalogue type ids (checkboxes; strings for Livewire binding). */
    public array $hotelTypeIds = [];

    /** Empty string = not set (HTML select). */
    public string $stars = '';

    public ?string $checkInTime = null;

    public ?string $checkOutTime = null;

    public ?string $saveMessage = null;

    public function mount(int $serviceId, int $serviceTypeId): void
    {
        $this->serviceId = $serviceId;
        $this->serviceTypeId = $serviceTypeId;

        $service = $this->authorizedService();
        $row = ServiceHotel::query()
            ->where('service_id', $service->id)
            ->with('hotelTypes')
            ->first();

        if ($row === null) {
            $this->hotelTypeIds = [];

            return;
        }

        $this->hotelTypeIds = $row->hotelTypes
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();
        $this->stars = $row->stars !== null ? (string) (int) $row->stars : '';
        $this->checkInTime = $this->normalizeTimeForInput($row->check_in_time);
        $this->checkOutTime = $this->normalizeTimeForInput($row->check_out_time);
    }

    public function save(): void
    {
        $this->saveMessage = null;
        $this->resetErrorBag();

        $typeIds = collect($this->hotelTypeIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $starsInt = $this->stars === '' || $this->stars === null ? null : (int) $this->stars;

        $checkInVal = ($this->checkInTime === null || $this->checkInTime === '') ? null : $this->checkInTime;
        $checkOutVal = ($this->checkOutTime === null || $this->checkOutTime === '') ? null : $this->checkOutTime;

        $typeTable = (new ServiceHotelType)->getTable();

        Validator::make(
            [
                'hotelTypeIds' => $typeIds,
                'stars' => $starsInt,
                'checkInTime' => $checkInVal,
                'checkOutTime' => $checkOutVal,
            ],
            [
                'hotelTypeIds' => ['required', 'array', 'min:1'],
                'hotelTypeIds.*' => ['integer', Rule::exists($typeTable, 'id')->where('active', true)],
                'stars' => ['nullable', 'integer', 'min:1', 'max:5'],
                'checkInTime' => ['nullable', 'date_format:H:i'],
                'checkOutTime' => ['nullable', 'date_format:H:i'],
            ],
            [],
            [
                'hotelTypeIds' => __('wizard.step7_hotel_field_types'),
                'hotelTypeIds.*' => __('wizard.step7_hotel_field_types'),
                'stars' => __('wizard.step7_hotel_field_stars'),
                'checkInTime' => __('wizard.step7_hotel_field_check_in'),
                'checkOutTime' => __('wizard.step7_hotel_field_check_out'),
            ]
        )->validate();

        $this->hotelTypeIds = collect($typeIds)->map(fn (int $id) => (string) $id)->values()->all();

        $service = $this->authorizedService();

        DB::transaction(function () use ($service, $typeIds, $starsInt, $checkInVal, $checkOutVal): void {
            $hotel = ServiceHotel::query()->updateOrCreate(
                ['service_id' => $service->id],
                [
                    'stars' => $starsInt,
                    'check_in_time' => $checkInVal,
                    'check_out_time' => $checkOutVal,
                ]
            );
            $hotel->hotelTypes()->sync($typeIds);
        });

        session()->flash('status', __('wizard.step7_hotel_saved'));

        $this->redirect(ServiceWizardStepEight::catalogServicesListUrl());
    }

    /**
     * @param  mixed  $value  DB time string or null
     */
    protected function normalizeTimeForInput(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_string($value) && preg_match('/^(\d{2}:\d{2})/', $value, $m)) {
            return $m[1];
        }

        return null;
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
        $categories = ServiceHotelTypeCategory::query()
            ->where('active', true)
            ->with([
                'translations.language.locale',
                'serviceHotelTypes' => fn ($q) => $q
                    ->where('active', true)
                    ->with(['translations.language.locale'])
                    ->orderBy('sort_order')
                    ->orderBy('id'),
            ])
            ->ordered()
            ->get()
            ->filter(fn (ServiceHotelTypeCategory $c) => $c->serviceHotelTypes->isNotEmpty())
            ->values();

        return view('livewire.service-wizard.service-hotel-advanced-step', [
            'categories' => $categories,
        ]);
    }
}
