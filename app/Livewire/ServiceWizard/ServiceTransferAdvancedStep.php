<?php

namespace App\Livewire\ServiceWizard;

use App\Models\Currency;
use App\Models\Service;
use App\Models\ServiceTransfer;
use App\Models\ServiceTransferLocation;
use App\Models\ServiceTransferLocationTypeCategory;
use App\Models\ServiceTransferPrice;
use App\Models\ServiceTransferRoute;
use App\Models\ServiceTransferVehicleType;
use App\Models\LmpCity;
use App\Services\AccountNotificationService;
use App\Services\PriceFormatService;
use App\Services\ServiceTransferLocationCatalogBootstrapService;
use App\Services\ServiceTransferVehicleCatalogBootstrapService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ServiceTransferAdvancedStep extends Component
{
    public int $serviceId;

    public int $serviceTypeId;

    public int $serviceTransferId;

    public string $activeTab = 'basics';

    public string $transfer_type = ServiceTransfer::TRANSFER_ONE_WAY;

    public string $modality = ServiceTransfer::MODALITY_PRIVATE;

    public bool $allows_multiple_stops = false;

    public bool $requires_flight_info = false;

    public bool $requires_pickup_time = false;

    public bool $requires_dropoff_time = false;

    /** @var int|string|null */
    public $max_passengers = null;

    /** @var int|string|null */
    public $max_luggage = null;

    /** @var int|string|null */
    public $default_duration_minutes = null;

    public int $newRouteOriginId = 0;

    public int $newRouteDestinationId = 0;

    public bool $new_route_is_active = true;

    public string $new_route_distance_km = '';

    public string $new_route_duration_minutes = '';

    public bool $showAddRouteModal = false;

    public bool $showAddPriceModal = false;

    /** Null when creating a route; set when editing an existing route in the modal. */
    public ?int $editingRouteId = null;

    /** Null when creating a price row; set when editing in the price modal. */
    public ?int $editingPriceId = null;

    public string $price_route_id = '';

    public string $price_vehicle_type_id = '';

    public string $price_pricing_type = ServiceTransferPrice::PRICING_PER_VEHICLE;

    public int $price_currency_id = 0;

    public string $price_base_price = '';

    public string $price_per_person = '';

    public string $price_per_extra_passenger = '';

    public string $price_min_passengers = '';

    public string $price_max_passengers = '';

    public string $price_valid_from = '';

    public string $price_valid_to = '';

    public ?string $saveMessage = null;

    public bool $showTransferVehicleBootstrapModal = false;

    public bool $showTransferLocationBootstrapModal = false;

    public bool $transferLocationBootstrapDismissed = false;

    public int $bootstrapLocationCityId = 0;

    /** @var list<string> */
    public array $bootstrapCatalogLocationIds = [];

    /** @var list<string> */
    public array $bootstrapCatalogCategoryIds = [];

    /** @var list<string> */
    public array $bootstrapCatalogTypeIds = [];

    public function mount(int $serviceId, int $serviceTypeId): void
    {
        $this->serviceId = $serviceId;
        $this->serviceTypeId = $serviceTypeId;

        $service = $this->authorizedService();

        $transfer = ServiceTransfer::query()->firstOrCreate(
            ['service_id' => $service->id],
            [
                'transfer_type' => ServiceTransfer::TRANSFER_ONE_WAY,
                'modality' => ServiceTransfer::MODALITY_PRIVATE,
                'allows_multiple_stops' => false,
                'requires_flight_info' => false,
                'requires_pickup_time' => false,
                'requires_dropoff_time' => false,
            ]
        );

        $this->serviceTransferId = (int) $transfer->id;
        $this->hydrateBasicsFromTransfer($transfer);

        if ($this->activeTab === 'vehicles') {
            $this->activeTab = 'routes';
        }

        $defaultCurrencyId = (int) Currency::query()->orderBy('id')->value('id');
        if ($defaultCurrencyId > 0) {
            $this->price_currency_id = $defaultCurrencyId;
        }

        $bootstrap = app(ServiceTransferVehicleCatalogBootstrapService::class);
        $this->showTransferVehicleBootstrapModal = $bootstrap->shouldShowBootstrapModal((int) $service->account_id);
        if ($this->showTransferVehicleBootstrapModal) {
            $this->initializeTransferVehicleBootstrapSelections();
        } else {
            $this->maybeOpenTransferLocationBootstrapModal();
        }
    }

    public function setTab(string $tab): void
    {
        $allowed = ['basics', 'routes', 'prices'];
        $this->activeTab = in_array($tab, $allowed, true) ? $tab : 'basics';
        $this->saveMessage = null;
        $this->showAddRouteModal = false;
        $this->showAddPriceModal = false;
        $this->resetNewRouteForm();
        $this->resetPriceModalForm();
    }

    public function updatedBootstrapCatalogCategoryIds(): void
    {
        $this->pruneBootstrapCatalogTypeSelection();
    }

    public function updatedBootstrapLocationCityId(): void
    {
        $this->pruneBootstrapCatalogLocationSelection();
    }

    public function selectAllBootstrapCatalogCategories(): void
    {
        $svc = app(ServiceTransferVehicleCatalogBootstrapService::class);
        $options = $svc->templateCategoryCheckboxOptions($svc->templateAccountId());
        $this->bootstrapCatalogCategoryIds = collect(array_keys($options))
            ->map(fn ($k) => (string) $k)
            ->values()
            ->all();
        $this->pruneBootstrapCatalogTypeSelection();
    }

    public function clearAllBootstrapCatalogCategories(): void
    {
        $this->bootstrapCatalogCategoryIds = [];
        $this->pruneBootstrapCatalogTypeSelection();
    }

    public function selectAllVisibleBootstrapCatalogTypes(): void
    {
        $this->bootstrapCatalogTypeIds = $this->getVisibleBootstrapCatalogTypeIdStrings();
    }

    public function selectAllTypesInBootstrapCategory(int $categoryId): void
    {
        $svc = app(ServiceTransferVehicleCatalogBootstrapService::class);
        $types = $svc->templateTypesGrouped($svc->templateAccountId())->get($categoryId, collect());
        $incoming = $types->pluck('id')->map(fn ($id) => (string) $id)->all();
        $this->bootstrapCatalogTypeIds = collect($this->bootstrapCatalogTypeIds)
            ->merge($incoming)
            ->unique()
            ->values()
            ->all();
        $this->pruneBootstrapCatalogTypeSelection();
    }

    public function clearTypesInBootstrapCategory(int $categoryId): void
    {
        $svc = app(ServiceTransferVehicleCatalogBootstrapService::class);
        $types = $svc->templateTypesGrouped($svc->templateAccountId())->get($categoryId, collect());
        $remove = $types->pluck('id')->flip();
        $this->bootstrapCatalogTypeIds = collect($this->bootstrapCatalogTypeIds)
            ->map(fn ($id) => (int) $id)
            ->reject(fn (int $id) => $remove->has($id))
            ->map(fn (int $id) => (string) $id)
            ->values()
            ->all();
    }

    public function clearAllBootstrapCatalogTypes(): void
    {
        $this->bootstrapCatalogTypeIds = [];
    }

    public function dismissTransferVehicleBootstrapModal(): void
    {
        $this->showTransferVehicleBootstrapModal = false;
        $this->saveMessage = null;
        $this->maybeOpenTransferLocationBootstrapModal();
    }

    public function applyTransferVehicleBootstrapImport(): void
    {
        $this->saveMessage = null;

        $service = $this->authorizedService();
        $svc = app(ServiceTransferVehicleCatalogBootstrapService::class);
        $templateAccountId = $svc->templateAccountId();

        $allowedIds = $svc->templateVehicleTypes($templateAccountId)->pluck('id')->all();

        Validator::make(
            ['bootstrapCatalogTypeIds' => $this->bootstrapCatalogTypeIds],
            [
                'bootstrapCatalogTypeIds' => ['required', 'array', 'min:1'],
                'bootstrapCatalogTypeIds.*' => ['required', 'string', Rule::in(collect($allowedIds)->map(fn ($id) => (string) $id)->all())],
            ],
            [],
            ['bootstrapCatalogTypeIds' => __('wizard.transfer_bootstrap_field_types')]
        )->validate();

        $selectedInts = collect($this->bootstrapCatalogTypeIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $imported = $svc->importTypesIntoAccount(
            $templateAccountId,
            (int) $service->account_id,
            $selectedInts
        );

        if ($imported < 1) {
            $this->addError('bootstrapCatalogTypeIds', __('wizard.transfer_bootstrap_none_added'));

            return;
        }

        app(AccountNotificationService::class)->createForAccount(
            accountId: (int) $service->account_id,
            type: 'transfer_vehicle_catalog_imported',
            title: (string) __('account.notifications.transfer_vehicle_catalog_imported_title'),
            message: (string) __('account.notifications.transfer_vehicle_catalog_imported_message', ['count' => $imported]),
            recipientUserId: null,
            data: [
                'imported_count' => $imported,
                'service_id' => $service->id,
            ],
        );

        $this->showTransferVehicleBootstrapModal = false;
        $this->saveMessage = (string) __('wizard.transfer_bootstrap_imported', ['count' => $imported]);
        $this->maybeOpenTransferLocationBootstrapModal();
    }

    public function dismissTransferLocationBootstrapModal(): void
    {
        $this->showTransferLocationBootstrapModal = false;
        $this->transferLocationBootstrapDismissed = true;
        $this->saveMessage = null;
    }

    public function reopenTransferLocationBootstrapModal(): void
    {
        $this->saveMessage = null;
        $this->initializeTransferLocationBootstrapSelections();
        $this->showTransferLocationBootstrapModal = true;
    }

    public function selectAllBootstrapCatalogLocations(): void
    {
        $cityId = (int) $this->bootstrapLocationCityId;
        if ($cityId < 1) {
            return;
        }
        $svc = app(ServiceTransferLocationCatalogBootstrapService::class);
        $this->bootstrapCatalogLocationIds = $svc->templateLocationIdStringsInCity($cityId);
    }

    public function clearAllBootstrapCatalogLocations(): void
    {
        $this->bootstrapCatalogLocationIds = [];
    }

    public function applyTransferLocationBootstrapImport(): void
    {
        $this->saveMessage = null;

        $service = $this->authorizedService();
        $svc = app(ServiceTransferLocationCatalogBootstrapService::class);
        $templateAccountId = $svc->templateAccountId();

        $cityId = (int) $this->bootstrapLocationCityId;
        $allowedIds = $svc->templateLocationIdStringsInCity($cityId);

        Validator::make(
            [
                'bootstrapLocationCityId' => $cityId,
                'bootstrapCatalogLocationIds' => $this->bootstrapCatalogLocationIds,
            ],
            [
                'bootstrapLocationCityId' => ['required', 'integer', 'min:1', Rule::exists(LmpCity::class, 'id')],
                'bootstrapCatalogLocationIds' => ['required', 'array', 'min:1'],
                'bootstrapCatalogLocationIds.*' => ['required', 'string', Rule::in($allowedIds)],
            ],
            [],
            [
                'bootstrapLocationCityId' => __('wizard.transfer_location_bootstrap_field_city'),
                'bootstrapCatalogLocationIds' => __('wizard.transfer_location_bootstrap_field_locations'),
            ]
        )->validate();

        $selectedInts = collect($this->bootstrapCatalogLocationIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $imported = $svc->importLocationsIntoAccount(
            $templateAccountId,
            (int) $service->account_id,
            $cityId,
            $selectedInts
        );

        if ($imported < 1) {
            $this->addError('bootstrapCatalogLocationIds', __('wizard.transfer_location_bootstrap_none_added'));

            return;
        }

        app(AccountNotificationService::class)->createForAccount(
            accountId: (int) $service->account_id,
            type: 'transfer_location_catalog_imported',
            title: (string) __('account.notifications.transfer_location_catalog_imported_title'),
            message: (string) __('account.notifications.transfer_location_catalog_imported_message', ['count' => $imported]),
            recipientUserId: null,
            data: [
                'imported_count' => $imported,
                'service_id' => $service->id,
                'city_id' => $cityId,
            ],
        );

        $this->showTransferLocationBootstrapModal = false;
        $this->saveMessage = (string) __('wizard.transfer_location_bootstrap_imported', ['count' => $imported]);
    }

    public function saveBasics(): void
    {
        $this->saveMessage = null;

        $maxPax = $this->normalizeOptionalUInt($this->max_passengers);
        $maxLug = $this->normalizeOptionalUInt($this->max_luggage);
        $defDur = $this->normalizeOptionalUInt($this->default_duration_minutes);

        Validator::make(
            [
                'transfer_type' => $this->transfer_type,
                'modality' => $this->modality,
                'allows_multiple_stops' => $this->allows_multiple_stops,
                'requires_flight_info' => $this->requires_flight_info,
                'requires_pickup_time' => $this->requires_pickup_time,
                'requires_dropoff_time' => $this->requires_dropoff_time,
                'max_passengers' => $maxPax,
                'max_luggage' => $maxLug,
                'default_duration_minutes' => $defDur,
            ],
            [
                'transfer_type' => ['required', Rule::in([ServiceTransfer::TRANSFER_ONE_WAY, ServiceTransfer::TRANSFER_ROUND_TRIP])],
                'modality' => ['required', Rule::in([ServiceTransfer::MODALITY_PRIVATE, ServiceTransfer::MODALITY_SHARED])],
                'allows_multiple_stops' => ['boolean'],
                'requires_flight_info' => ['boolean'],
                'requires_pickup_time' => ['boolean'],
                'requires_dropoff_time' => ['boolean'],
                'max_passengers' => ['nullable', 'integer', 'min:0', 'max:500'],
                'max_luggage' => ['nullable', 'integer', 'min:0', 'max:500'],
                'default_duration_minutes' => ['nullable', 'integer', 'min:0', 'max:100000'],
            ],
            [],
            [
                'transfer_type' => __('wizard.step7_transfer_field_transfer_type'),
                'modality' => __('wizard.step7_transfer_field_modality'),
                'max_passengers' => __('wizard.step7_transfer_field_max_passengers'),
                'max_luggage' => __('wizard.step7_transfer_field_max_luggage'),
                'default_duration_minutes' => __('wizard.step7_transfer_field_default_duration'),
            ]
        )->validate();

        $transfer = $this->loadAuthorizedTransfer();
        $transfer->update([
            'transfer_type' => $this->transfer_type,
            'modality' => $this->modality,
            'allows_multiple_stops' => $this->allows_multiple_stops,
            'requires_flight_info' => $this->requires_flight_info,
            'requires_pickup_time' => $this->requires_pickup_time,
            'requires_dropoff_time' => $this->requires_dropoff_time,
            'max_passengers' => $maxPax,
            'max_luggage' => $maxLug,
            'default_duration_minutes' => $defDur,
        ]);

        $this->max_passengers = $maxPax;
        $this->max_luggage = $maxLug;
        $this->default_duration_minutes = $defDur;

        $this->saveMessage = __('wizard.step7_transfer_saved_basics');
    }

    public function openAddRouteModal(): void
    {
        $this->saveMessage = null;
        $this->resetValidation();
        $this->resetNewRouteForm();
        $this->showAddRouteModal = true;
    }

    public function openEditRouteModal(int $routeId): void
    {
        $this->saveMessage = null;
        $this->resetValidation();

        $transfer = $this->loadAuthorizedTransfer();
        $route = ServiceTransferRoute::query()
            ->whereKey($routeId)
            ->where('service_transfer_id', $transfer->id)
            ->firstOrFail();

        $this->editingRouteId = (int) $route->id;
        $this->newRouteOriginId = (int) $route->origin_location_id;
        $this->newRouteDestinationId = (int) $route->destination_location_id;
        $this->new_route_is_active = (bool) $route->is_active;
        $this->new_route_distance_km = $route->distance_km !== null ? (string) $route->distance_km : '';
        $this->new_route_duration_minutes = $route->duration_minutes !== null ? (string) $route->duration_minutes : '';

        $this->showAddRouteModal = true;
    }

    public function closeAddRouteModal(): void
    {
        $this->showAddRouteModal = false;
        $this->resetNewRouteForm();
    }

    public function openAddPriceModal(): void
    {
        $this->saveMessage = null;
        $this->resetPriceModalForm();
        $this->resetValidation();
        $this->showAddPriceModal = true;
    }

    public function openEditPriceModal(int $priceId): void
    {
        $this->saveMessage = null;
        $this->resetValidation();

        $transfer = $this->loadAuthorizedTransfer();
        $price = ServiceTransferPrice::query()
            ->whereKey($priceId)
            ->where('service_transfer_id', $transfer->id)
            ->firstOrFail();

        $this->editingPriceId = (int) $price->id;
        $this->price_route_id = $price->route_id !== null ? (string) (int) $price->route_id : '';
        $this->price_vehicle_type_id = $price->service_transfer_vehicle_type_id !== null ? (string) (int) $price->service_transfer_vehicle_type_id : '';
        $this->price_pricing_type = (string) $price->pricing_type;
        $this->price_currency_id = (int) $price->currency_id;
        $this->price_base_price = $price->base_price !== null ? (string) $price->base_price : '';
        $this->price_per_person = $price->price_per_person !== null ? (string) $price->price_per_person : '';
        $this->price_per_extra_passenger = $price->price_per_extra_passenger !== null ? (string) $price->price_per_extra_passenger : '';
        $this->price_min_passengers = $price->min_passengers !== null ? (string) (int) $price->min_passengers : '';
        $this->price_max_passengers = $price->max_passengers !== null ? (string) (int) $price->max_passengers : '';
        $this->price_valid_from = $price->valid_from !== null ? $price->valid_from->format('Y-m-d') : '';
        $this->price_valid_to = $price->valid_to !== null ? $price->valid_to->format('Y-m-d') : '';

        $this->showAddPriceModal = true;
    }

    public function closeAddPriceModal(): void
    {
        $this->showAddPriceModal = false;
        $this->resetPriceModalForm();
    }

    protected function resetPriceModalForm(): void
    {
        $this->editingPriceId = null;
        $this->price_route_id = '';
        $this->price_vehicle_type_id = '';
        $this->price_pricing_type = ServiceTransferPrice::PRICING_PER_VEHICLE;
        $defaultCurrencyId = (int) Currency::query()->orderBy('id')->value('id');
        $this->price_currency_id = $defaultCurrencyId > 0 ? $defaultCurrencyId : 0;
        $this->price_base_price = '';
        $this->price_per_person = '';
        $this->price_per_extra_passenger = '';
        $this->price_min_passengers = '';
        $this->price_max_passengers = '';
        $this->price_valid_from = '';
        $this->price_valid_to = '';
    }

    protected function resetNewRouteForm(): void
    {
        $this->editingRouteId = null;
        $this->newRouteOriginId = 0;
        $this->newRouteDestinationId = 0;
        $this->new_route_is_active = true;
        $this->new_route_distance_km = '';
        $this->new_route_duration_minutes = '';
    }

    public function saveRoute(): void
    {
        $this->saveMessage = null;

        $service = $this->authorizedService();
        $accountId = (int) $service->account_id;

        $originId = (int) $this->newRouteOriginId;
        $destId = (int) $this->newRouteDestinationId;
        $locTable = (new ServiceTransferLocation)->getTable();

        $originExistsRule = Rule::exists($locTable, 'id')
            ->where('is_active', true)
            ->where('account_id', $accountId);
        $destExistsRule = Rule::exists($locTable, 'id')
            ->where('is_active', true)
            ->where('account_id', $accountId);
        if ($service->city_id !== null) {
            $originExistsRule = $originExistsRule->where('city_id', (int) $service->city_id);
            $destExistsRule = $destExistsRule->where('city_id', (int) $service->city_id);
        }

        Validator::make(
            [
                'origin' => $originId,
                'destination' => $destId,
                'new_route_distance_km' => $this->new_route_distance_km,
                'new_route_duration_minutes' => $this->new_route_duration_minutes,
                'new_route_is_active' => $this->new_route_is_active,
            ],
            [
                'origin' => ['required', 'integer', $originExistsRule],
                'destination' => ['required', 'integer', $destExistsRule, 'different:origin'],
                'new_route_distance_km' => ['required', 'numeric', 'min:0', 'max:99999'],
                'new_route_duration_minutes' => ['required', 'integer', 'min:0', 'max:100000'],
                'new_route_is_active' => ['boolean'],
            ],
            [],
            [
                'origin' => __('wizard.step7_transfer_route_origin'),
                'destination' => __('wizard.step7_transfer_route_destination'),
                'new_route_distance_km' => __('wizard.step7_transfer_field_route_distance_km'),
                'new_route_duration_minutes' => __('wizard.step7_transfer_field_route_duration_min'),
                'new_route_is_active' => __('wizard.step7_transfer_route_active'),
            ]
        )->validate();

        $distanceKm = is_numeric($this->new_route_distance_km)
            ? number_format((float) $this->new_route_distance_km, 2, '.', '')
            : '0.00';
        $durationMin = (int) $this->new_route_duration_minutes;

        $transfer = $this->loadAuthorizedTransfer();

        $duplicateQuery = ServiceTransferRoute::query()
            ->where('service_transfer_id', $transfer->id)
            ->where('origin_location_id', $originId)
            ->where('destination_location_id', $destId);

        if ($this->editingRouteId !== null) {
            $duplicateQuery->where('id', '!=', $this->editingRouteId);
        }

        if ($duplicateQuery->exists()) {
            $this->addError('origin', __('wizard.step7_transfer_route_duplicate'));

            return;
        }

        if ($this->editingRouteId !== null) {
            $route = ServiceTransferRoute::query()
                ->whereKey($this->editingRouteId)
                ->where('service_transfer_id', $transfer->id)
                ->firstOrFail();

            $route->update([
                'origin_location_id' => $originId,
                'destination_location_id' => $destId,
                'is_active' => (bool) $this->new_route_is_active,
                'distance_km' => $distanceKm,
                'duration_minutes' => $durationMin,
            ]);

            $this->saveMessage = __('wizard.step7_transfer_updated_route');
        } else {
            ServiceTransferRoute::query()->create([
                'service_transfer_id' => $transfer->id,
                'origin_location_id' => $originId,
                'destination_location_id' => $destId,
                'is_active' => (bool) $this->new_route_is_active,
                'distance_km' => $distanceKm,
                'duration_minutes' => $durationMin,
            ]);

            $this->saveMessage = __('wizard.step7_transfer_saved_route');
        }

        $this->closeAddRouteModal();
    }

    public function removeRoute(int $routeId): void
    {
        $this->saveMessage = null;
        $transfer = $this->loadAuthorizedTransfer();

        ServiceTransferRoute::query()
            ->whereKey($routeId)
            ->where('service_transfer_id', $transfer->id)
            ->delete();

        ServiceTransferPrice::query()
            ->where('service_transfer_id', $transfer->id)
            ->where('route_id', $routeId)
            ->delete();

        $this->saveMessage = __('wizard.step7_transfer_removed_route');
    }

    public function savePrice(): void
    {
        $this->saveMessage = null;

        $service = $this->authorizedService();
        $transfer = $this->loadAuthorizedTransfer();

        $routeId = $this->price_route_id === '' ? null : (int) $this->price_route_id;
        if ($routeId !== null) {
            $ownsRoute = ServiceTransferRoute::query()
                ->whereKey($routeId)
                ->where('service_transfer_id', $transfer->id)
                ->exists();
            if (! $ownsRoute) {
                $this->addError('price_route_id', __('wizard.step7_transfer_price_invalid_route'));

                return;
            }
        }

        $vehicleTypeId = $this->price_vehicle_type_id === '' ? null : (int) $this->price_vehicle_type_id;
        $vtTable = (new ServiceTransferVehicleType)->getTable();

        $base = $this->normalizeOptionalDecimal($this->price_base_price);
        $ppp = $this->normalizeOptionalDecimal($this->price_per_person);
        $ppe = $this->normalizeOptionalDecimal($this->price_per_extra_passenger);
        $minP = $this->normalizeOptionalUInt($this->price_min_passengers);
        $maxP = $this->normalizeOptionalUInt($this->price_max_passengers);
        $validFrom = $this->normalizeOptionalDate($this->price_valid_from);
        $validTo = $this->normalizeOptionalDate($this->price_valid_to);

        if ($validFrom !== null && $validTo !== null && strtotime($validTo) < strtotime($validFrom)) {
            $this->addError('price_valid_to', __('wizard.step7_transfer_price_valid_range'));

            return;
        }

        $rules = [
            'price_pricing_type' => ['required', Rule::in([ServiceTransferPrice::PRICING_PER_VEHICLE, ServiceTransferPrice::PRICING_PER_PERSON])],
            'price_currency_id' => ['required', 'integer', 'min:1', Rule::exists((new Currency)->getTable(), 'id')],
            'price_vehicle_type_id' => [
                'nullable',
                'integer',
                Rule::exists($vtTable, 'id')->where('account_id', $service->account_id),
            ],
            'price_base_price' => ['nullable', 'numeric', 'min:0'],
            'price_per_person' => ['nullable', 'numeric', 'min:0'],
            'price_per_extra_passenger' => ['nullable', 'numeric', 'min:0'],
            'price_min_passengers' => ['nullable', 'integer', 'min:0', 'max:500'],
            'price_max_passengers' => ['nullable', 'integer', 'min:0', 'max:500'],
            'price_valid_from' => ['nullable', 'date'],
            'price_valid_to' => ['nullable', 'date'],
        ];

        Validator::make(
            [
                'price_pricing_type' => $this->price_pricing_type,
                'price_currency_id' => $this->price_currency_id,
                'price_base_price' => $base,
                'price_per_person' => $ppp,
                'price_per_extra_passenger' => $ppe,
                'price_min_passengers' => $minP,
                'price_max_passengers' => $maxP,
                'price_valid_from' => $validFrom,
                'price_valid_to' => $validTo,
                'price_vehicle_type_id' => $vehicleTypeId,
            ],
            $rules,
            [],
            [
                'price_currency_id' => __('wizard.step7_transfer_field_currency'),
                'price_base_price' => __('wizard.step7_transfer_field_base_price'),
                'price_per_person' => __('wizard.step7_transfer_field_price_per_person'),
            ]
        )->validate();

        if ($this->price_pricing_type === ServiceTransferPrice::PRICING_PER_VEHICLE && $base === null) {
            $this->addError('price_base_price', __('wizard.step7_transfer_price_need_base'));

            return;
        }

        if ($this->price_pricing_type === ServiceTransferPrice::PRICING_PER_PERSON && $ppp === null) {
            $this->addError('price_per_person', __('wizard.step7_transfer_price_need_per_person'));

            return;
        }

        $payload = [
            'route_id' => $routeId,
            'service_transfer_vehicle_type_id' => $vehicleTypeId,
            'pricing_type' => $this->price_pricing_type,
            'currency_id' => $this->price_currency_id,
            'base_price' => $base,
            'price_per_person' => $ppp,
            'price_per_extra_passenger' => $ppe,
            'min_passengers' => $minP,
            'max_passengers' => $maxP,
            'valid_from' => $validFrom,
            'valid_to' => $validTo,
        ];

        if ($this->editingPriceId !== null) {
            $price = ServiceTransferPrice::query()
                ->whereKey($this->editingPriceId)
                ->where('service_transfer_id', $transfer->id)
                ->firstOrFail();
            $price->update($payload);
            $this->saveMessage = __('wizard.step7_transfer_updated_price');
        } else {
            ServiceTransferPrice::query()->create(array_merge(
                ['service_transfer_id' => $transfer->id],
                $payload
            ));
            $this->saveMessage = __('wizard.step7_transfer_saved_price');
        }

        $this->closeAddPriceModal();
    }

    public function removePrice(int $priceId): void
    {
        $this->saveMessage = null;
        $transfer = $this->loadAuthorizedTransfer();

        ServiceTransferPrice::query()
            ->whereKey($priceId)
            ->where('service_transfer_id', $transfer->id)
            ->delete();

        $this->saveMessage = __('wizard.step7_transfer_removed_price');
    }

    protected function hydrateBasicsFromTransfer(ServiceTransfer $transfer): void
    {
        $this->transfer_type = (string) $transfer->transfer_type;
        $this->modality = (string) $transfer->modality;
        $this->allows_multiple_stops = (bool) $transfer->allows_multiple_stops;
        $this->requires_flight_info = (bool) $transfer->requires_flight_info;
        $this->requires_pickup_time = (bool) $transfer->requires_pickup_time;
        $this->requires_dropoff_time = (bool) $transfer->requires_dropoff_time;
        $this->max_passengers = $transfer->max_passengers;
        $this->max_luggage = $transfer->max_luggage;
        $this->default_duration_minutes = $transfer->default_duration_minutes;
    }

    protected function loadAuthorizedTransfer(): ServiceTransfer
    {
        $service = $this->authorizedService();

        return ServiceTransfer::query()
            ->whereKey($this->serviceTransferId)
            ->where('service_id', $service->id)
            ->firstOrFail();
    }

    public function formatTransferPrice(float|string|null $amount, ?Currency $currency = null): string
    {
        $accountId = Auth::user()?->currentAccountId();

        return app(PriceFormatService::class)->formatWithCurrency(
            $amount,
            $currency,
            accountId: $accountId !== null ? (int) $accountId : null,
        );
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

    /**
     * @param  mixed  $value
     */
    protected function normalizeOptionalUInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    /**
     * @param  mixed  $value
     */
    protected function normalizeOptionalDecimal(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (string) $value : null;
    }

    protected function normalizeOptionalDate(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        return $value;
    }

    protected function initializeTransferVehicleBootstrapSelections(): void
    {
        $this->bootstrapCatalogCategoryIds = [];
        $this->bootstrapCatalogTypeIds = [];
    }

    protected function initializeTransferLocationBootstrapSelections(): void
    {
        $this->bootstrapLocationCityId = 0;
        $this->bootstrapCatalogLocationIds = [];
    }

    /**
     * Opens the transfer location import dialog when the account has no catalogue locations yet
     * (and the vehicle bootstrap modal is not blocking).
     */
    protected function maybeOpenTransferLocationBootstrapModal(): void
    {
        if ($this->showTransferVehicleBootstrapModal) {
            return;
        }
        if ($this->transferLocationBootstrapDismissed) {
            return;
        }
        $service = $this->authorizedService();
        $svc = app(ServiceTransferLocationCatalogBootstrapService::class);
        if (! $svc->shouldShowBootstrapModal((int) $service->account_id)) {
            return;
        }
        $this->showTransferLocationBootstrapModal = true;
        $this->initializeTransferLocationBootstrapSelections();
    }

    /**
     * Drop location selections that do not belong to the currently selected template city.
     */
    protected function pruneBootstrapCatalogLocationSelection(): void
    {
        $cityId = (int) $this->bootstrapLocationCityId;
        if ($cityId < 1) {
            $this->bootstrapCatalogLocationIds = [];

            return;
        }
        $svc = app(ServiceTransferLocationCatalogBootstrapService::class);
        $allowed = collect($svc->templateLocationIdStringsInCity($cityId))->flip();
        $this->bootstrapCatalogLocationIds = collect($this->bootstrapCatalogLocationIds)
            ->map(fn ($id) => (string) $id)
            ->filter(fn (string $id) => $allowed->has($id))
            ->values()
            ->all();
    }

    /**
     * Template vehicle type IDs currently visible under the category filter (string ids for checkboxes).
     *
     * @return list<string>
     */
    protected function getVisibleBootstrapCatalogTypeIdStrings(): array
    {
        $svc = app(ServiceTransferVehicleCatalogBootstrapService::class);
        $grouped = $svc->templateTypesGrouped($svc->templateAccountId());
        $categoryIdsInt = collect($this->bootstrapCatalogCategoryIds)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($categoryIdsInt === []) {
            return [];
        }

        $ids = [];
        foreach ($categoryIdsInt as $cid) {
            foreach ($grouped->get($cid, collect()) as $t) {
                $ids[] = (string) $t->id;
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * Remove type selections that are not visible under the current category filter.
     */
    protected function pruneBootstrapCatalogTypeSelection(): void
    {
        $allowed = collect($this->getVisibleBootstrapCatalogTypeIdStrings())->flip();
        $this->bootstrapCatalogTypeIds = collect($this->bootstrapCatalogTypeIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $allowed->has((string) $id))
            ->map(fn (int $id) => (string) $id)
            ->values()
            ->all();
    }

    /**
     * Group locations by their location type's category for route selects: categories follow {@see ServiceTransferLocationTypeCategory} sort_order, locations A–Z by label.
     *
     * @return list<array{category: ?ServiceTransferLocationTypeCategory, locations: Collection<int, ServiceTransferLocation>}>
     */
    protected function buildLocationRouteGroups(Collection $locations): array
    {
        if ($locations->isEmpty()) {
            return [];
        }

        /** @var array<int, list<ServiceTransferLocation>> $bucket */
        $bucket = [];
        foreach ($locations as $loc) {
            $cid = (int) ($loc->locationType?->service_transfer_location_type_category_id ?? 0);
            if (! array_key_exists($cid, $bucket)) {
                $bucket[$cid] = [];
            }
            $bucket[$cid][] = $loc;
        }

        $positiveIds = collect(array_keys($bucket))
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->values()
            ->all();

        $orderedCategories = $positiveIds === []
            ? collect()
            : ServiceTransferLocationTypeCategory::query()
                ->whereIn('id', $positiveIds)
                ->where('active', true)
                ->ordered()
                ->with(['translations.language.locale'])
                ->get();

        $blocks = [];
        foreach ($orderedCategories as $category) {
            $id = (int) $category->id;
            if (empty($bucket[$id])) {
                continue;
            }
            $blocks[] = [
                'category' => $category,
                'locations' => collect($bucket[$id])->sortBy(
                    fn (ServiceTransferLocation $l): string => mb_strtolower($l->wizardRouteSelectLabel(), 'UTF-8')
                )->values(),
            ];
            unset($bucket[$id]);
        }

        $remaining = [];
        foreach ($bucket as $locs) {
            $remaining = array_merge($remaining, $locs);
        }

        if ($remaining !== []) {
            $blocks[] = [
                'category' => null,
                'locations' => collect($remaining)->sortBy(
                    fn (ServiceTransferLocation $l): string => mb_strtolower($l->wizardRouteSelectLabel(), 'UTF-8')
                )->values(),
            ];
        }

        return $blocks;
    }

    public function render(): View
    {
        $service = $this->authorizedService();

        $transfer = ServiceTransfer::query()
            ->whereKey($this->serviceTransferId)
            ->where('service_id', $service->id)
            ->with([
                'routes.origin.translations.language.locale',
                'routes.destination.translations.language.locale',
                'routes.origin.city',
                'routes.destination.city',
                'prices.currency.lmpCurrency',
                'prices.route.origin.translations.language.locale',
                'prices.route.destination.translations.language.locale',
                'prices.vehicleType',
            ])
            ->firstOrFail();

        $locationsQuery = ServiceTransferLocation::query()
            ->where('account_id', $service->account_id)
            ->where('is_active', true)
            ->with(['translations.language.locale', 'city', 'locationType.translations.language.locale', 'locationType.category.translations.language.locale'])
            ->orderBy('id');

        if ($service->city_id !== null) {
            $locationsQuery->where('city_id', $service->city_id);
        }

        $locations = $locationsQuery->get();

        $locationRouteGroups = $this->buildLocationRouteGroups($locations);

        $locBootstrapSvc = app(ServiceTransferLocationCatalogBootstrapService::class);
        $transferLocationBootstrapTemplateEmpty = ! $locBootstrapSvc->templateHasImportableLocations();
        $locationBootstrapCityOptions = $locBootstrapSvc->templateCityOptions();

        $bootstrapModalLocations = collect();
        if ($this->showTransferLocationBootstrapModal && (int) $this->bootstrapLocationCityId > 0) {
            $bootstrapModalLocations = $locBootstrapSvc->templateLocationsInCity((int) $this->bootstrapLocationCityId);
        }

        $vehicleTypes = ServiceTransferVehicleType::query()
            ->where('account_id', $service->account_id)
            ->where('active', true)
            ->orderBy('name')
            ->orderBy('id')
            ->get();

        $currencies = Currency::query()->with('lmpCurrency')->orderBy('id')->get();

        $bootstrapModalCategoryOptions = [];
        $bootstrapModalGrouped = collect();
        if ($this->showTransferVehicleBootstrapModal) {
            $bootSvc = app(ServiceTransferVehicleCatalogBootstrapService::class);
            $tid = $bootSvc->templateAccountId();
            $bootstrapModalCategoryOptions = $bootSvc->templateCategoryCheckboxOptions($tid);
            $bootstrapModalGrouped = $bootSvc->orderedTemplateTypesGrouped($tid);
        }

        return view('livewire.service-wizard.service-transfer-advanced-step', [
            'transfer' => $transfer,
            'serviceCityId' => $service->city_id,
            'locations' => $locations,
            'locationRouteGroups' => $locationRouteGroups,
            'vehicleTypes' => $vehicleTypes,
            'currencies' => $currencies,
            'bootstrapModalCategoryOptions' => $bootstrapModalCategoryOptions,
            'bootstrapModalGrouped' => $bootstrapModalGrouped,
            'transferLocationBootstrapTemplateEmpty' => $transferLocationBootstrapTemplateEmpty,
            'locationBootstrapCityOptions' => $locationBootstrapCityOptions,
            'bootstrapModalLocations' => $bootstrapModalLocations,
        ]);
    }
}
