<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ServiceTransferVehicleType;
use App\Models\ServiceTransferVehicleTypeCategory;
use App\Services\AccountNotificationService;
use App\Services\AccountTransferVehicleTypeMutationGuard;
use App\Services\ServiceTransferVehicleCatalogBootstrapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class AccountTransferVehicleTypesController extends Controller
{
    public function __construct(
        private readonly AccountTransferVehicleTypeMutationGuard $mutationGuard,
    ) {
    }

    public function index(Request $request): View
    {
        $account = $this->resolveCurrentAccount($request);

        $vehicleTypes = ServiceTransferVehicleType::query()
            ->where('account_id', $account->id)
            ->with('category.translations.language.locale')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(25);

        $bootstrapSvc = app(ServiceTransferVehicleCatalogBootstrapService::class);
        $importCatalogAvailable = $bootstrapSvc->systemCatalogHasVehicleTypes();
        $importCatalogCategoryOptions = [];
        $importCatalogGrouped = collect();
        if ($importCatalogAvailable) {
            $importCatalogCategoryOptions = $bootstrapSvc->systemCategoryCheckboxOptions();
            $importCatalogGrouped = $bootstrapSvc->orderedSystemTypesGrouped();
        }

        return view('account.transfer-vehicle-types.index', [
            'account' => $account,
            'vehicleTypes' => $vehicleTypes,
            'importCatalogAvailable' => $importCatalogAvailable,
            'importCatalogCategoryOptions' => $importCatalogCategoryOptions,
            'importCatalogGrouped' => $importCatalogGrouped,
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $account = $this->resolveCurrentAccount($request);
        $bootstrap = app(ServiceTransferVehicleCatalogBootstrapService::class);

        if (! $bootstrap->systemCatalogHasVehicleTypes()) {
            return redirect()
                ->route('account.transfer-vehicle-types.index')
                ->with('error', __('account.transfer_vehicle_types.import_template_empty'));
        }

        $allowedIds = $bootstrap->systemVehicleTypes()->pluck('id')->all();

        $validated = $request->validate(
            [
                'template_type_ids' => ['required', 'array', 'min:1'],
                'template_type_ids.*' => ['required', 'integer', Rule::in($allowedIds)],
            ],
            [],
            [
                'template_type_ids' => __('account.transfer_vehicle_types.import_field_types'),
            ]
        );

        $imported = $bootstrap->importTypesIntoAccount(
            (int) $account->id,
            $validated['template_type_ids']
        );

        if ($imported < 1) {
            return redirect()
                ->route('account.transfer-vehicle-types.index')
                ->with('error', __('account.transfer_vehicle_types.import_none_added'));
        }

        app(AccountNotificationService::class)->createForAccount(
            accountId: (int) $account->id,
            type: 'transfer_vehicle_catalog_imported',
            title: (string) __('account.notifications.transfer_vehicle_catalog_imported_title'),
            message: (string) __('account.notifications.transfer_vehicle_catalog_imported_message', ['count' => $imported]),
            recipientUserId: null,
            data: [
                'imported_count' => $imported,
            ],
        );

        return redirect()
            ->route('account.transfer-vehicle-types.index')
            ->with('status', __('account.transfer_vehicle_types.import_success', ['count' => $imported]));
    }

    public function create(Request $request): View
    {
        $account = $this->resolveCurrentAccount($request);

        return view('account.transfer-vehicle-types.form', [
            'account' => $account,
            'vehicleType' => null,
            'categories' => $this->categoryOptions(),
            'submitRoute' => route('account.transfer-vehicle-types.store'),
            'submitMethod' => 'POST',
            'cancelRoute' => route('account.transfer-vehicle-types.index'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $account = $this->resolveCurrentAccount($request);
        $validated = $this->validatePayload($request, $account->id, null);

        ServiceTransferVehicleType::query()->create([
            'account_id' => $account->id,
            'code' => $validated['code'],
            'service_transfer_vehicle_type_category_id' => $validated['service_transfer_vehicle_type_category_id'],
            'name' => $validated['name'],
            'sort_order' => (int) $validated['sort_order'],
            'max_passengers' => $validated['max_passengers'],
            'max_luggage' => $validated['max_luggage'],
            'active' => (bool) $validated['active'],
        ]);

        return redirect()
            ->route('account.transfer-vehicle-types.index')
            ->with('status', __('account.transfer_vehicle_types.status_created'));
    }

    public function edit(Request $request, ServiceTransferVehicleType $transfer_vehicle_type): View
    {
        $account = $this->resolveCurrentAccount($request);
        $this->assertBelongsToAccount($transfer_vehicle_type, $account->id);

        return view('account.transfer-vehicle-types.form', [
            'account' => $account,
            'vehicleType' => $transfer_vehicle_type,
            'categories' => $this->categoryOptions(),
            'submitRoute' => route('account.transfer-vehicle-types.update', $transfer_vehicle_type),
            'submitMethod' => 'PUT',
            'cancelRoute' => route('account.transfer-vehicle-types.index'),
        ]);
    }

    public function update(Request $request, ServiceTransferVehicleType $transfer_vehicle_type): RedirectResponse
    {
        $account = $this->resolveCurrentAccount($request);
        $this->assertBelongsToAccount($transfer_vehicle_type, $account->id);

        $this->mutationGuard->assertCanUpdate($transfer_vehicle_type);

        $validated = $this->validatePayload($request, $account->id, $transfer_vehicle_type->id);

        $transfer_vehicle_type->update([
            'code' => $validated['code'],
            'service_transfer_vehicle_type_category_id' => $validated['service_transfer_vehicle_type_category_id'],
            'name' => $validated['name'],
            'sort_order' => (int) $validated['sort_order'],
            'max_passengers' => $validated['max_passengers'],
            'max_luggage' => $validated['max_luggage'],
            'active' => (bool) $validated['active'],
        ]);

        return redirect()
            ->route('account.transfer-vehicle-types.index')
            ->with('status', __('account.transfer_vehicle_types.status_updated'));
    }

    public function destroy(Request $request, ServiceTransferVehicleType $transfer_vehicle_type): RedirectResponse
    {
        $account = $this->resolveCurrentAccount($request);
        $this->assertBelongsToAccount($transfer_vehicle_type, $account->id);

        $this->mutationGuard->assertCanDelete($transfer_vehicle_type);

        if ($transfer_vehicle_type->transferVehicles()->exists() || $transfer_vehicle_type->prices()->exists()) {
            return redirect()
                ->route('account.transfer-vehicle-types.index')
                ->with('error', __('account.transfer_vehicle_types.delete_blocked_in_use'));
        }

        $transfer_vehicle_type->delete();

        return redirect()
            ->route('account.transfer-vehicle-types.index')
            ->with('status', __('account.transfer_vehicle_types.status_deleted'));
    }

    private function resolveCurrentAccount(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        return $account;
    }

    private function assertBelongsToAccount(ServiceTransferVehicleType $vehicleType, int $accountId): void
    {
        abort_unless((int) $vehicleType->account_id === $accountId, 404);
    }

    /**
     * @return \Illuminate\Support\Collection<int, ServiceTransferVehicleTypeCategory>
     */
    private function categoryOptions()
    {
        return ServiceTransferVehicleTypeCategory::query()
            ->where('active', true)
            ->ordered()
            ->with(['translations.language.locale'])
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, int $accountId, ?int $ignoreId): array
    {
        $codeInput = $request->input('code');
        if ($codeInput === '' || $codeInput === null) {
            $request->merge(['code' => null]);
        } elseif (is_string($codeInput)) {
            $request->merge(['code' => trim($codeInput)]);
        }

        $catTable = (new ServiceTransferVehicleTypeCategory)->getTable();

        $rules = [
            'code' => [
                'nullable',
                'string',
                'max:120',
                Rule::unique('service_transfer_vehicle_types', 'code')
                    ->where('account_id', $accountId)
                    ->ignore($ignoreId),
            ],
            'service_transfer_vehicle_type_category_id' => [
                'nullable',
                'integer',
                Rule::exists($catTable, 'id')->where('active', true),
            ],
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:99999'],
            'max_passengers' => ['nullable', 'integer', 'min:0', 'max:500'],
            'max_luggage' => ['nullable', 'integer', 'min:0', 'max:500'],
            'active' => ['sometimes', 'boolean'],
        ];

        $validated = $request->validate($rules, [], [
            'code' => __('account.transfer_vehicle_types.fields.code'),
            'service_transfer_vehicle_type_category_id' => __('account.transfer_vehicle_types.fields.category'),
            'name' => __('account.transfer_vehicle_types.fields.name'),
            'sort_order' => __('account.transfer_vehicle_types.fields.sort_order'),
            'max_passengers' => __('account.transfer_vehicle_types.fields.max_passengers'),
            'max_luggage' => __('account.transfer_vehicle_types.fields.max_luggage'),
            'active' => __('account.transfer_vehicle_types.fields.active'),
        ]);

        $validated['active'] = $request->boolean('active');
        $validated['max_passengers'] = array_key_exists('max_passengers', $validated) && $validated['max_passengers'] !== null && $validated['max_passengers'] !== ''
            ? (int) $validated['max_passengers']
            : null;
        $validated['max_luggage'] = array_key_exists('max_luggage', $validated) && $validated['max_luggage'] !== null && $validated['max_luggage'] !== ''
            ? (int) $validated['max_luggage']
            : null;
        $validated['service_transfer_vehicle_type_category_id'] = isset($validated['service_transfer_vehicle_type_category_id']) && $validated['service_transfer_vehicle_type_category_id'] !== '' && (int) $validated['service_transfer_vehicle_type_category_id'] > 0
            ? (int) $validated['service_transfer_vehicle_type_category_id']
            : null;

        return $validated;
    }
}
