<?php

namespace App\Livewire\ServiceWizard;

use App\Models\LmpCity;
use App\Models\Service;
use App\Models\ServiceGastronomy;
use App\Models\ServiceGastronomyCuisine;
use App\Models\ServiceGastronomyExperience;
use App\Models\ServiceGastronomyMenu;
use App\Models\ServiceGastronomyType;
use App\Models\ServiceGastronomyVenue;
use App\Services\Geocoding\NominatimGeocoder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ServiceGastronomyAdvancedStep extends Component
{
    private const MIN_CITY_SEARCH_LENGTH = 4;

    private const MAX_CITY_SEARCH_RESULTS = 50;

    public int $serviceId;

    public int $serviceTypeId;

    public string $activeTab = 'types';

    /** @var array<int, string> */
    public array $gastronomyTypeIds = [];

    public bool $is_indoor = false;

    public bool $is_outdoor = false;

    public bool $has_takeaway = false;

    public bool $has_delivery = false;

    /** @var array<int, string> */
    public array $cuisineIds = [];

    /** @var array<int, string> */
    public array $venueIds = [];

    /** @var array<int, string> */
    public array $menuIds = [];

    public string $locationCityQuery = '';

    /** @var list<array{id: int, label: string}> */
    public array $locationCityResults = [];

    public ?int $locationCityId = null;

    public string $locationCityDisplayLabel = '';

    public string $address = '';

    public ?string $latitude = null;

    public ?string $longitude = null;

    /** @var int|null|string Empty string may arrive from the number input before save normalizes it. */
    public $experienceDurationMinutes = null;

    public bool $experienceIncludesFood = false;

    public bool $experienceIncludesDrinks = false;

    public bool $experienceIsGuided = false;

    public ?string $geocodeFeedback = null;

    public bool $geocodeSuccess = false;

    public ?string $citySearchNotice = null;

    /** Shown after a successful save (session flash outside Livewire is not visible on AJAX updates). */
    public ?string $saveMessage = null;

    public function mount(int $serviceId, int $serviceTypeId): void
    {
        $this->serviceId = $serviceId;
        $this->serviceTypeId = $serviceTypeId;

        $service = $this->authorizedService();
        $gastro = ServiceGastronomy::query()
            ->where('service_id', $service->id)
            ->with(['gastronomyTypes', 'cuisines', 'venues', 'menus', 'experience'])
            ->first();

        if ($gastro === null) {
            $this->gastronomyTypeIds = [];
            $this->experienceDurationMinutes = null;
            $this->experienceIncludesFood = false;
            $this->experienceIncludesDrinks = false;
            $this->experienceIsGuided = false;
            $this->hydrateLocationCityFromId($service->city_id, $service);

            return;
        }

        $this->gastronomyTypeIds = $gastro->gastronomyTypes->pluck('id')->map(fn ($id) => (string) $id)->values()->all();
        $this->is_indoor = (bool) $gastro->is_indoor;
        $this->is_outdoor = (bool) $gastro->is_outdoor;
        $this->has_takeaway = (bool) $gastro->has_takeaway;
        $this->has_delivery = (bool) $gastro->has_delivery;

        $this->address = (string) ($gastro->address ?? '');
        $this->latitude = $gastro->latitude !== null ? (string) $gastro->latitude : null;
        $this->longitude = $gastro->longitude !== null ? (string) $gastro->longitude : null;

        $this->cuisineIds = $gastro->cuisines->pluck('id')->map(fn ($id) => (string) $id)->values()->all();
        $this->venueIds = $gastro->venues->pluck('id')->map(fn ($id) => (string) $id)->values()->all();
        $this->menuIds = $gastro->menus->pluck('id')->map(fn ($id) => (string) $id)->values()->all();

        $this->hydrateLocationCityFromId($gastro->city_id, $service);

        $exp = $gastro->experience;
        if ($exp !== null) {
            $this->experienceDurationMinutes = $exp->duration_minutes !== null ? (int) $exp->duration_minutes : null;
            $this->experienceIncludesFood = (bool) $exp->includes_food;
            $this->experienceIncludesDrinks = (bool) $exp->includes_drinks;
            $this->experienceIsGuided = (bool) $exp->is_guided;
        }
    }

    protected function hydrateLocationCityFromId(?int $cityId, Service $service): void
    {
        $id = $cityId !== null && $cityId > 0 ? $cityId : null;
        if ($id === null) {
            $this->locationCityId = null;
            $this->locationCityDisplayLabel = '';

            return;
        }

        $city = LmpCity::query()->with(['state.country'])->find($id);
        if ($city === null) {
            $this->locationCityId = null;
            $this->locationCityDisplayLabel = '';

            return;
        }

        $this->locationCityId = (int) $city->id;
        $this->locationCityDisplayLabel = $this->formatCitySearchLabel($city);
    }

    public function updatedLocationCityQuery(): void
    {
        $q = trim($this->locationCityQuery);
        if (mb_strlen($q) < self::MIN_CITY_SEARCH_LENGTH) {
            $this->locationCityResults = [];
            $this->citySearchNotice = null;

            return;
        }

        $cities = LmpCity::query()
            ->with(['state.country'])
            ->where('name', 'like', '%'.$q.'%')
            ->orderBy('name')
            ->limit(self::MAX_CITY_SEARCH_RESULTS + 1)
            ->get(['id', 'name', 'state_id']);

        $truncated = $cities->count() > self::MAX_CITY_SEARCH_RESULTS;
        if ($truncated) {
            $cities = $cities->take(self::MAX_CITY_SEARCH_RESULTS);
        }

        $this->locationCityResults = $cities->map(fn (LmpCity $city) => [
            'id' => (int) $city->id,
            'label' => $this->formatCitySearchLabel($city),
        ])->values()->all();

        $this->citySearchNotice = $truncated
            ? __('wizard.step7_city_search_truncated', ['max' => self::MAX_CITY_SEARCH_RESULTS])
            : null;
    }

    public function selectLocationCity(int $cityId): void
    {
        $city = LmpCity::query()->with(['state.country'])->find($cityId);
        if ($city === null) {
            return;
        }

        $this->locationCityId = $cityId;
        $this->locationCityDisplayLabel = $this->formatCitySearchLabel($city);
        $this->locationCityQuery = '';
        $this->locationCityResults = [];
        $this->geocodeFeedback = null;
        $this->geocodeSuccess = false;
    }

    public function clearLocationCity(): void
    {
        $this->locationCityId = null;
        $this->locationCityDisplayLabel = '';
        $this->locationCityQuery = '';
        $this->locationCityResults = [];
        $this->geocodeFeedback = null;
        $this->geocodeSuccess = false;
        $this->citySearchNotice = null;
    }

    public function suggestCoordinatesFromAddress(NominatimGeocoder $geocoder): void
    {
        $this->geocodeFeedback = null;
        $this->geocodeSuccess = false;

        if (! config('services.nominatim.enabled')) {
            $this->geocodeFeedback = __('wizard.step7_geocode_disabled');

            return;
        }

        if ($this->locationCityId === null || $this->locationCityId < 1) {
            $this->geocodeFeedback = __('wizard.step7_geocode_need_city');

            return;
        }

        $addressLine = trim($this->address);
        if ($addressLine === '') {
            $this->geocodeFeedback = __('wizard.step7_geocode_need_address');

            return;
        }

        $city = LmpCity::query()->with(['state.country'])->find($this->locationCityId);
        if ($city === null) {
            $this->geocodeFeedback = __('wizard.step7_geocode_need_city');

            return;
        }

        $result = $geocoder->firstResultForServiceLocation($addressLine, $city);
        if ($result === null) {
            $this->geocodeFeedback = __('wizard.step7_geocode_none');

            return;
        }

        $this->latitude = (string) $result['lat'];
        $this->longitude = (string) $result['lon'];
        $this->geocodeFeedback = __('wizard.step7_geocode_success');
        $this->geocodeSuccess = true;
    }

    public function setTab(string $tab): void
    {
        $allowed = ['types', 'basics', 'cuisines', 'venues', 'menus', 'experience'];
        $this->activeTab = in_array($tab, $allowed, true) ? $tab : 'basics';
    }

    public function save(): void
    {
        $this->saveMessage = null;

        $service = $this->authorizedService();

        if ($this->experienceDurationMinutes === '' || $this->experienceDurationMinutes === null) {
            $this->experienceDurationMinutes = null;
        } else {
            $this->experienceDurationMinutes = (int) $this->experienceDurationMinutes;
        }

        $this->latitude = $this->latitude === '' ? null : $this->latitude;
        $this->longitude = $this->longitude === '' ? null : $this->longitude;

        $typeTable = (new ServiceGastronomyType)->getTable();
        $cuisineTable = (new ServiceGastronomyCuisine)->getTable();
        $venueTable = (new ServiceGastronomyVenue)->getTable();
        $menuTable = (new ServiceGastronomyMenu)->getTable();

        $typeSync = collect($this->gastronomyTypeIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $rules = [
            'gastronomyTypeIds' => ['required', 'array', 'min:1'],
            'gastronomyTypeIds.*' => ['required', Rule::exists($typeTable, 'id')->where('active', true)],
            'locationCityId' => ['nullable', 'integer', Rule::exists('addons.lmp_cities', 'id')],
            'address' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_indoor' => ['boolean'],
            'is_outdoor' => ['boolean'],
            'has_takeaway' => ['boolean'],
            'has_delivery' => ['boolean'],
            'cuisineIds' => ['array'],
            'cuisineIds.*' => ['required', Rule::exists($cuisineTable, 'id')->where('active', true)],
            'venueIds' => ['array'],
            'venueIds.*' => ['required', Rule::exists($venueTable, 'id')->where('active', true)],
            'menuIds' => ['array'],
            'menuIds.*' => ['required', Rule::exists($menuTable, 'id')->where('active', true)],
            'experienceDurationMinutes' => ['nullable', 'integer', 'min:0', 'max:10080'],
            'experienceIncludesFood' => ['boolean'],
            'experienceIncludesDrinks' => ['boolean'],
            'experienceIsGuided' => ['boolean'],
        ];

        $this->validate($rules, [], [
            'gastronomyTypeIds' => __('wizard.step7_field_gastronomy_types'),
            'gastronomyTypeIds.*' => __('wizard.step7_field_gastronomy_type'),
            'locationCityId' => __('wizard.step7_field_city'),
            'address' => __('wizard.step7_field_address'),
            'latitude' => __('wizard.step7_field_latitude'),
            'longitude' => __('wizard.step7_field_longitude'),
        ]);

        $latFloat = $this->normalizeOptionalCoordinate($this->latitude);
        $lonFloat = $this->normalizeOptionalCoordinate($this->longitude);

        $cuisineSync = collect($this->cuisineIds)->map(fn ($id) => (int) $id)->filter(fn (int $id) => $id > 0)->unique()->values()->all();
        $venueSync = collect($this->venueIds)->map(fn ($id) => (int) $id)->filter(fn (int $id) => $id > 0)->unique()->values()->all();
        $menuSync = collect($this->menuIds)->map(fn ($id) => (int) $id)->filter(fn (int $id) => $id > 0)->unique()->values()->all();

        DB::transaction(function () use ($service, $typeSync, $cuisineSync, $venueSync, $menuSync, $latFloat, $lonFloat): void {
            $gastro = ServiceGastronomy::query()->updateOrCreate(
                ['service_id' => $service->id],
                [
                    'city_id' => $this->locationCityId,
                    'address' => $this->address !== '' ? $this->address : null,
                    'latitude' => $latFloat,
                    'longitude' => $lonFloat,
                    'is_indoor' => $this->is_indoor,
                    'is_outdoor' => $this->is_outdoor,
                    'has_takeaway' => $this->has_takeaway,
                    'has_delivery' => $this->has_delivery,
                ]
            );

            $gastro->gastronomyTypes()->sync($typeSync);
            $gastro->cuisines()->sync($cuisineSync);
            $gastro->venues()->sync($venueSync);
            $gastro->menus()->sync($menuSync);

            ServiceGastronomyExperience::query()->updateOrCreate(
                ['service_gastronomy_id' => $gastro->id],
                [
                    'duration_minutes' => $this->experienceDurationMinutes,
                    'includes_food' => $this->experienceIncludesFood,
                    'includes_drinks' => $this->experienceIncludesDrinks,
                    'is_guided' => $this->experienceIsGuided,
                ]
            );
        });

        $this->gastronomyTypeIds = collect($typeSync)->map(fn (int $id) => (string) $id)->values()->all();
        $this->latitude = $latFloat !== null ? (string) $latFloat : null;
        $this->longitude = $lonFloat !== null ? (string) $lonFloat : null;

        $this->saveMessage = __('wizard.step7_saved');
    }

    protected function normalizeOptionalCoordinate(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }

    protected function formatCitySearchLabel(LmpCity $city): string
    {
        $stateName = $city->state?->name;
        $countryName = $city->state?->country?->name;
        $tail = array_filter([$stateName, $countryName], fn ($v) => $v !== null && $v !== '');

        if ($tail === []) {
            return $city->name;
        }

        return $city->name.' — '.implode(', ', $tail);
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
        $types = ServiceGastronomyType::query()
            ->where('active', true)
            ->with(['translations.language.locale'])
            ->ordered()
            ->get();

        $cuisines = ServiceGastronomyCuisine::query()
            ->where('active', true)
            ->with(['translations.language.locale'])
            ->ordered()
            ->get();

        $venues = ServiceGastronomyVenue::query()
            ->where('active', true)
            ->with(['translations.language.locale'])
            ->ordered()
            ->get();

        $menus = ServiceGastronomyMenu::query()
            ->where('active', true)
            ->with(['translations.language.locale'])
            ->ordered()
            ->get();

        return view('livewire.service-wizard.service-gastronomy-advanced-step', [
            'types' => $types,
            'cuisines' => $cuisines,
            'venues' => $venues,
            'menus' => $menus,
        ]);
    }
}
