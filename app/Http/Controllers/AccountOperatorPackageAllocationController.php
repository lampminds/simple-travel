<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountRelationship;
use App\Models\OperatorServiceCatalog;
use App\Models\PackageAllocation;
use App\Services\PackageAllocationInventoryCapacityService;
use App\Services\PackageAllocationValidationService;
use App\Support\AccountBusinessTypeGate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class AccountOperatorPackageAllocationController extends Controller
{
    public function __construct(
        private readonly PackageAllocationValidationService $allocationValidation,
        private readonly PackageAllocationInventoryCapacityService $inventoryCapacity,
    ) {
    }

    public function agenciesIndex(Request $request): View
    {
        $account = $this->resolveOperatorAccount($request);

        $relationships = AccountRelationship::query()
            ->where('operator_account_id', $account->id)
            ->where('status', AccountRelationship::STATUS_APPROVED)
            ->with('providerAccount')
            ->orderBy('id')
            ->get();

        $countsByAgency = PackageAllocation::query()
            ->where('operator_id', $account->id)
            ->selectRaw('agency_id, COUNT(*) as aggregate')
            ->groupBy('agency_id')
            ->pluck('aggregate', 'agency_id');

        foreach ($relationships as $relationship) {
            $agencyId = (int) $relationship->provider_account_id;
            $relationship->setAttribute('allocations_count', (int) ($countsByAgency[$agencyId] ?? 0));
        }

        return view('account.package-allocations.operator.agencies', [
            'account' => $account,
            'relationships' => $relationships,
        ]);
    }

    public function index(Request $request, Account $agency): View
    {
        $account = $this->resolveOperatorAccount($request);
        $this->assertApprovedAgencyRelationship($account, $agency);

        $allocations = PackageAllocation::query()
            ->where('operator_id', $account->id)
            ->where('agency_id', $agency->id)
            ->with([
                'catalog.translations.language.locale',
            ])
            ->orderByDesc('active')
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->get();

        foreach ($allocations as $allocation) {
            $allocation->setAttribute(
                'target_label',
                $this->allocationValidation->allocationTargetLabel($allocation),
            );
        }

        return view('account.package-allocations.operator.index', [
            'account' => $account,
            'agency' => $agency,
            'allocations' => $allocations,
        ]);
    }

    public function create(Request $request, Account $agency): View
    {
        $account = $this->resolveOperatorAccount($request);
        $this->assertApprovedAgencyRelationship($account, $agency);

        $targetOptions = $this->allocationValidation->eligibleTargetOptions((int) $account->id, (int) $agency->id);

        $selectedTargetKey = old('target_key');
        if ($selectedTargetKey === null) {
            $catalogId = $request->integer('catalog');
            if ($catalogId > 0 && array_key_exists($catalogId, $targetOptions['catalogs'] ?? [])) {
                $selectedTargetKey = 'catalog:'.$catalogId;
            }
        }

        return view('account.package-allocations.operator.form', [
            'account' => $account,
            'agency' => $agency,
            'allocation' => null,
            'targetOptions' => $targetOptions,
            'selectedTargetKey' => $selectedTargetKey,
            'submitRoute' => route('account.package-allocations.agencies.store', $agency),
            'submitMethod' => 'POST',
            'cancelRoute' => route('account.package-allocations.agencies.index', $agency),
        ]);
    }

    public function store(Request $request, Account $agency): RedirectResponse
    {
        $account = $this->resolveOperatorAccount($request);
        $this->assertApprovedAgencyRelationship($account, $agency);

        $payload = $this->validatePayload($request, (int) $account->id, (int) $agency->id);

        PackageAllocation::query()->create([
            'operator_id' => $account->id,
            'agency_id' => $agency->id,
            ...$payload,
        ]);

        return redirect()
            ->route('account.package-allocations.agencies.index', $agency)
            ->with('status', __('account.package_allocations.status_created'));
    }

    public function edit(Request $request, PackageAllocation $allocation): View
    {
        $account = $this->resolveOperatorAccount($request);
        $this->assertAllocationBelongsToOperator($allocation, (int) $account->id);

        $allocation->load([
            'catalog.translations.language.locale',
        ]);

        $agency = $allocation->agencyAccount;
        abort_unless($agency instanceof Account, 404);

        $targetOptions = $this->allocationValidation->eligibleTargetOptions((int) $account->id, (int) $agency->id);
        $targetOptions = $this->mergeCurrentAllocationTarget($allocation, $targetOptions);

        return view('account.package-allocations.operator.form', [
            'account' => $account,
            'agency' => $agency,
            'allocation' => $allocation,
            'targetOptions' => $targetOptions,
            'selectedTargetKey' => old('target_key', $this->allocationValidation->targetKeyFromAllocation($allocation)),
            'submitRoute' => route('account.package-allocations.update', $allocation),
            'submitMethod' => 'PUT',
            'cancelRoute' => route('account.package-allocations.agencies.index', $agency),
        ]);
    }

    public function update(Request $request, PackageAllocation $allocation): RedirectResponse
    {
        $account = $this->resolveOperatorAccount($request);
        $this->assertAllocationBelongsToOperator($allocation, (int) $account->id);

        $agencyId = (int) $allocation->agency_id;
        $payload = $this->validatePayload(
            $request,
            (int) $account->id,
            $agencyId,
            (int) $allocation->id,
        );

        $allocation->update($payload);

        $agency = $allocation->agencyAccount;
        abort_unless($agency instanceof Account, 404);

        return redirect()
            ->route('account.package-allocations.agencies.index', $agency)
            ->with('status', __('account.package_allocations.status_updated'));
    }

    public function destroy(Request $request, PackageAllocation $allocation): RedirectResponse
    {
        $account = $this->resolveOperatorAccount($request);
        $this->assertAllocationBelongsToOperator($allocation, (int) $account->id);

        $agency = $allocation->agencyAccount;
        abort_unless($agency instanceof Account, 404);

        $allocation->delete();

        return redirect()
            ->route('account.package-allocations.agencies.index', $agency)
            ->with('status', __('account.package_allocations.status_deleted'));
    }

    /**
     * @return array{
     *     operator_service_catalog_id: int,
     *     allocation_type: string,
     *     capacity: int,
     *     start_date: string|null,
     *     end_date: string|null,
     *     active: bool
     * }
     */
    private function validatePayload(
        Request $request,
        int $operatorId,
        int $agencyId,
        ?int $excludeAllocationId = null,
    ): array {
        normalize_request_locale_dates($request, ['start_date', 'end_date']);

        $validated = $request->validate([
            'target_key' => ['required', 'string'],
            'allocation_type' => ['required', Rule::in([
                PackageAllocation::TYPE_HARD,
                PackageAllocation::TYPE_SOFT,
                PackageAllocation::TYPE_FREE_SALE,
            ])],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'active' => ['nullable', 'boolean'],
        ]);

        $parsed = $this->allocationValidation->parseTargetKey((string) $validated['target_key']);
        if ($parsed === null) {
            throw ValidationException::withMessages([
                'target_key' => __('account.package_allocations.validation.target_invalid'),
            ]);
        }

        $catalogId = $parsed['operator_service_catalog_id'];

        if ($catalogId <= 0) {
            throw ValidationException::withMessages([
                'target_key' => __('account.package_allocations.validation.target_invalid'),
            ]);
        }

        $targetOptions = $this->allocationValidation->eligibleTargetOptions($operatorId, $agencyId);
        if (! isset($targetOptions['catalogs'][$catalogId])) {
            throw ValidationException::withMessages([
                'target_key' => __('account.package_allocations.validation.target_not_offered'),
            ]);
        }

        $catalog = OperatorServiceCatalog::query()
            ->whereKey($catalogId)
            ->where('operator_id', $operatorId)
            ->first();
        if ($catalog === null) {
            throw ValidationException::withMessages([
                'target_key' => __('account.package_allocations.validation.target_not_owned'),
            ]);
        }

        if (! $this->allocationValidation->targetHasAcceptedOffer($operatorId, $agencyId, $catalogId)) {
            $existing = $excludeAllocationId !== null
                ? PackageAllocation::query()->find($excludeAllocationId)
                : null;

            $targetUnchanged = $existing instanceof PackageAllocation
                && (int) $existing->operator_service_catalog_id === $catalogId;

            if (! $targetUnchanged) {
                throw ValidationException::withMessages([
                    'target_key' => __('account.package_allocations.validation.target_not_accepted'),
                ]);
            }
        }

        $allocationType = (string) $validated['allocation_type'];
        $capacity = $allocationType === PackageAllocation::TYPE_FREE_SALE
            ? 0
            : (int) ($validated['capacity'] ?? 0);

        if ($allocationType !== PackageAllocation::TYPE_FREE_SALE && $capacity < 1) {
            throw ValidationException::withMessages([
                'capacity' => __('account.package_allocations.validation.capacity_required'),
            ]);
        }

        $startDate = isset($validated['start_date']) ? (string) $validated['start_date'] : null;
        $endDate = isset($validated['end_date']) ? (string) $validated['end_date'] : null;

        $overlap = $this->allocationValidation->findOverlappingAllocation(
            $operatorId,
            $agencyId,
            $catalogId,
            $startDate,
            $endDate,
            $excludeAllocationId,
        );

        if ($overlap instanceof PackageAllocation) {
            throw ValidationException::withMessages([
                'start_date' => __('account.package_allocations.validation.date_overlap'),
            ]);
        }

        $inventoryViolation = $this->inventoryCapacity->findInventoryViolation(
            $catalog,
            $capacity,
            $startDate,
            $endDate,
            (bool) ($validated['active'] ?? false),
            $allocationType,
            $excludeAllocationId,
        );

        if ($inventoryViolation !== null) {
            $message = ($inventoryViolation['reason'] ?? '') === 'missing_inventory_total'
                ? __('account.package_allocations.validation.inventory_not_defined')
                : __('account.package_allocations.validation.capacity_exceeds_inventory', [
                    'date' => $inventoryViolation['date'] !== ''
                        ? locale_date($inventoryViolation['date'])
                        : __('account.package_allocations.validity_open'),
                    'assigned' => number_format((int) $inventoryViolation['assigned']),
                    'limit' => number_format((int) $inventoryViolation['limit']),
                ]);

            throw ValidationException::withMessages([
                'capacity' => $message,
            ]);
        }

        return [
            'operator_service_catalog_id' => $catalogId,
            'allocation_type' => $allocationType,
            'capacity' => $capacity,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'active' => (bool) ($validated['active'] ?? false),
        ];
    }

    private function resolveOperatorAccount(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($user->hasRoleForCurrentAccount('owner'), 403);

        return AccountBusinessTypeGate::assertOperatorAccount($request);
    }

    private function assertApprovedAgencyRelationship(Account $operator, Account $agency): void
    {
        abort_unless(
            AccountRelationship::query()
                ->where('operator_account_id', $operator->id)
                ->where('provider_account_id', $agency->id)
                ->where('status', AccountRelationship::STATUS_APPROVED)
                ->exists(),
            404
        );
    }

    private function assertAllocationBelongsToOperator(PackageAllocation $allocation, int $operatorId): void
    {
        abort_unless((int) $allocation->operator_id === $operatorId, 404);
    }

    /**
     * @param  array{catalogs: array<int, string>}  $targetOptions
     * @return array{catalogs: array<int, string>}
     */
    private function mergeCurrentAllocationTarget(PackageAllocation $allocation, array $targetOptions): array
    {
        $catalogId = (int) $allocation->operator_service_catalog_id;
        if (! isset($targetOptions['catalogs'][$catalogId])) {
            $targetOptions['catalogs'][$catalogId] = $this->allocationValidation->allocationTargetLabel($allocation);
        }

        return $targetOptions;
    }
}
