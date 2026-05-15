<?php

namespace App\Livewire\ServiceWizard;

use App\Models\Service;
use App\Models\ServiceHotel;
use App\Models\ServiceHotelType;
use App\Models\ServiceHotelTypeCategory;
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

    /** Selected catalogue type ids (checkboxes). */
    public array $hotelTypeIds = [];

    /** Empty string = not set (HTML select). */
    public string $stars = '';

    public ?string $checkInTime = null;

    public ?string $checkOutTime = null;

    /** @var int|string|null */
    public $roomsCount = null;

    public string $chainName = '';

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

        $this->hotelTypeIds = $row->hotelTypes->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
        $this->stars = $row->stars !== null ? (string) (int) $row->stars : '';
        $this->checkInTime = $this->normalizeTimeForInput($row->check_in_time);
        $this->checkOutTime = $this->normalizeTimeForInput($row->check_out_time);
        $this->roomsCount = $row->rooms_count !== null ? (int) $row->rooms_count : null;
        $this->chainName = (string) ($row->chain_name ?? '');
    }

    public function save(): void
    {
        $this->saveMessage = null;

        $typeIds = collect($this->hotelTypeIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $starsInt = $this->stars === '' || $this->stars === null ? null : (int) $this->stars;

        $roomsCountVal = $this->roomsCount;
        if ($roomsCountVal === '' || $roomsCountVal === null) {
            $roomsCountVal = null;
        } else {
            $roomsCountVal = (int) $roomsCountVal;
        }
        $this->roomsCount = $roomsCountVal;

        $checkInVal = ($this->checkInTime === null || $this->checkInTime === '') ? null : $this->checkInTime;
        $checkOutVal = ($this->checkOutTime === null || $this->checkOutTime === '') ? null : $this->checkOutTime;

        $typeTable = (new ServiceHotelType)->getTable();

        Validator::make(
            [
                'hotelTypeIds' => $typeIds,
                'stars' => $starsInt,
                'checkInTime' => $checkInVal,
                'checkOutTime' => $checkOutVal,
                'roomsCount' => $roomsCountVal,
                'chainName' => $this->chainName,
            ],
            [
                'hotelTypeIds' => ['required', 'array', 'min:1'],
                'hotelTypeIds.*' => ['integer', Rule::exists($typeTable, 'id')->where('active', true)],
                'stars' => ['nullable', 'integer', 'min:1', 'max:5'],
                'checkInTime' => ['nullable', 'date_format:H:i'],
                'checkOutTime' => ['nullable', 'date_format:H:i'],
                'roomsCount' => ['nullable', 'integer', 'min:0', 'max:100000'],
                'chainName' => ['nullable', 'string', 'max:255'],
            ],
            [],
            [
                'hotelTypeIds' => __('wizard.step7_hotel_field_types'),
                'hotelTypeIds.*' => __('wizard.step7_hotel_field_types'),
                'stars' => __('wizard.step7_hotel_field_stars'),
                'checkInTime' => __('wizard.step7_hotel_field_check_in'),
                'checkOutTime' => __('wizard.step7_hotel_field_check_out'),
                'roomsCount' => __('wizard.step7_hotel_field_rooms'),
                'chainName' => __('wizard.step7_hotel_field_chain'),
            ]
        )->validate();

        $this->hotelTypeIds = $typeIds;

        $service = $this->authorizedService();

        DB::transaction(function () use ($service, $typeIds, $starsInt, $checkInVal, $checkOutVal, $roomsCountVal): void {
            $hotel = ServiceHotel::query()->updateOrCreate(
                ['service_id' => $service->id],
                [
                    'stars' => $starsInt,
                    'check_in_time' => $checkInVal,
                    'check_out_time' => $checkOutVal,
                    'rooms_count' => $roomsCountVal,
                    'chain_name' => trim($this->chainName) !== '' ? trim($this->chainName) : null,
                ]
            );
            $hotel->hotelTypes()->sync($typeIds);
        });

        $this->saveMessage = __('wizard.step7_hotel_saved');
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
