<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountRelationship;
use App\Models\Allocation;
use App\Models\Service;
use App\Models\ServiceVariant;
use App\Services\AllocationInventoryCapacityService;
use App\Services\AllocationValidationService;
use App\Support\AccountBusinessTypeGate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class AccountProviderAllocationController extends Controller
{
    public function __construct(
        private readonly AllocationValidationService $allocationValidation,
        private readonly AllocationInventoryCapacityService $inventoryCapacity,
    ) {
    }

    public function operatorsIndex(Request $request): View
    {
        $account = $this->resolveProviderAccount($request);

        $relationships = AccountRelationship::query()
            ->where('provider_account_id', $account->id)
            ->where('status', AccountRelationship::STATUS_APPROVED)
            ->with('operatorAccount')
            ->orderBy('id')
            ->get();

        $countsByOperator = Allocation::query()
            ->where('provider_id', $account->id)
            ->selectRaw('operator_id, COUNT(*) as aggregate')
            ->groupBy('operator_id')
            ->pluck('aggregate', 'operator_id');

        foreach ($relationships as $relationship) {
            $operatorId = (int) $relationship->operator_account_id;
            $relationship->setAttribute('allocations_count', (int) ($countsByOperator[$operatorId] ?? 0));
        }

        return view('account.allocations.provider.operators', [
            'account' => $account,
            'relationships' => $relationships,
        ]);
    }

    public function index(Request $request, Account $operator): View
    {
        $account = $this->resolveProviderAccount($request);
        $this->assertApprovedOperatorRelationship($account, $operator);

        $allocations = Allocation::query()
            ->where('provider_id', $account->id)
            ->where('operator_id', $operator->id)
            ->with([
                'service.translations.language.locale',
                'serviceVariant.translations.language.locale',
                'serviceVariant.service.translations.language.locale',
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

        return view('account.allocations.provider.index', [
            'account' => $account,
            'operator' => $operator,
            'allocations' => $allocations,
        ]);
    }

    public function create(Request $request, Account $operator): View
    {
        $account = $this->resolveProviderAccount($request);
        $this->assertApprovedOperatorRelationship($account, $operator);

        $targetOptions = $this->allocationValidation->eligibleTargetOptions((int) $account->id, (int) $operator->id);

        return view('account.allocations.provider.form', [
            'account' => $account,
            'operator' => $operator,
            'allocation' => null,
            'targetOptions' => $targetOptions,
            'selectedTargetKey' => old('target_key'),
            'submitRoute' => route('account.allocations.operators.store', $operator),
            'submitMethod' => 'POST',
            'cancelRoute' => route('account.allocations.operators.index', $operator),
        ]);
    }

    public function store(Request $request, Account $operator): RedirectResponse
    {
        $account = $this->resolveProviderAccount($request);
        $this->assertApprovedOperatorRelationship($account, $operator);

        $payload = $this->validatePayload($request, (int) $account->id, (int) $operator->id);

        Allocation::query()->create([
            'provider_id' => $account->id,
            'operator_id' => $operator->id,
            ...$payload,
        ]);

        return redirect()
            ->route('account.allocations.operators.index', $operator)
            ->with('status', __('account.allocations.status_created'));
    }

    public function edit(Request $request, Allocation $allocation): View
    {
        $account = $this->resolveProviderAccount($request);
        $this->assertAllocationBelongsToProvider($allocation, (int) $account->id);

        $allocation->load([
            'service.translations.language.locale',
            'serviceVariant.translations.language.locale',
            'serviceVariant.service.translations.language.locale',
        ]);

        $operator = $allocation->operatorAccount;
        abort_unless($operator instanceof Account, 404);

        $targetOptions = $this->allocationValidation->eligibleTargetOptions((int) $account->id, (int) $operator->id);
        $targetOptions = $this->mergeCurrentAllocationTarget($allocation, $targetOptions);

        return view('account.allocations.provider.form', [
            'account' => $account,
            'operator' => $operator,
            'allocation' => $allocation,
            'targetOptions' => $targetOptions,
            'selectedTargetKey' => old('target_key', $this->allocationValidation->targetKeyFromAllocation($allocation)),
            'submitRoute' => route('account.allocations.update', $allocation),
            'submitMethod' => 'PUT',
            'cancelRoute' => route('account.allocations.operators.index', $operator),
        ]);
    }

    public function update(Request $request, Allocation $allocation): RedirectResponse
    {
        $account = $this->resolveProviderAccount($request);
        $this->assertAllocationBelongsToProvider($allocation, (int) $account->id);

        $operatorId = (int) $allocation->operator_id;
        $payload = $this->validatePayload(
            $request,
            (int) $account->id,
            $operatorId,
            (int) $allocation->id,
        );

        $allocation->update($payload);

        $operator = $allocation->operatorAccount;
        abort_unless($operator instanceof Account, 404);

        return redirect()
            ->route('account.allocations.operators.index', $operator)
            ->with('status', __('account.allocations.status_updated'));
    }

    public function destroy(Request $request, Allocation $allocation): RedirectResponse
    {
        $account = $this->resolveProviderAccount($request);
        $this->assertAllocationBelongsToProvider($allocation, (int) $account->id);

        $operator = $allocation->operatorAccount;
        abort_unless($operator instanceof Account, 404);

        $allocation->delete();

        return redirect()
            ->route('account.allocations.operators.index', $operator)
            ->with('status', __('account.allocations.status_deleted'));
    }

    /**
     * @return array{
     *     service_variant_id: int,
     *     allocation_type: string,
     *     capacity: int,
     *     start_date: string|null,
     *     end_date: string|null,
     *     active: bool
     * }
     */
    private function validatePayload(
        Request $request,
        int $providerId,
        int $operatorId,
        ?int $excludeAllocationId = null,
    ): array {
        normalize_request_locale_dates($request, ['start_date', 'end_date']);

        $validated = $request->validate([
            'target_key' => ['required', 'string'],
            'allocation_type' => ['required', Rule::in([
                Allocation::TYPE_HARD,
                Allocation::TYPE_SOFT,
                Allocation::TYPE_FREE_SALE,
            ])],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'active' => ['nullable', 'boolean'],
        ]);

        $parsed = $this->allocationValidation->parseTargetKey((string) $validated['target_key']);
        if ($parsed === null) {
            throw ValidationException::withMessages([
                'target_key' => __('account.allocations.validation.target_invalid'),
            ]);
        }

        $serviceId = $parsed['service_variant_id'];

        if ($serviceId <= 0) {
            throw ValidationException::withMessages([
                'target_key' => __('account.allocations.validation.target_invalid'),
            ]);
        }

        $targetOptions = $this->allocationValidation->eligibleTargetOptions($providerId, $operatorId);
        if (! isset($targetOptions['variants'][$serviceId])) {
            throw ValidationException::withMessages([
                'target_key' => __('account.allocations.validation.target_not_offered'),
            ]);
        }

        $variant = ServiceVariant::query()
            ->whereKey($serviceId)
            ->whereHas('service', fn ($q) => $q->where('account_id', $providerId))
            ->first();
        if ($variant === null) {
            throw ValidationException::withMessages([
                'target_key' => __('account.allocations.validation.target_not_owned'),
            ]);
        }

        if (! $this->allocationValidation->targetHasAcceptedOffer($providerId, $operatorId, $serviceId)) {
            $existing = $excludeAllocationId !== null
                ? Allocation::query()->find($excludeAllocationId)
                : null;

            $targetUnchanged = $existing instanceof Allocation
                && (int) $existing->service_variant_id === $serviceId;

            if (! $targetUnchanged) {
                throw ValidationException::withMessages([
                    'target_key' => __('account.allocations.validation.target_not_accepted'),
                ]);
            }
        }

        $allocationType = (string) $validated['allocation_type'];
        $capacity = $allocationType === Allocation::TYPE_FREE_SALE
            ? 0
            : (int) ($validated['capacity'] ?? 0);

        if ($allocationType !== Allocation::TYPE_FREE_SALE && $capacity < 1) {
            throw ValidationException::withMessages([
                'capacity' => __('account.allocations.validation.capacity_required'),
            ]);
        }

        $startDate = isset($validated['start_date']) ? (string) $validated['start_date'] : null;
        $endDate = isset($validated['end_date']) ? (string) $validated['end_date'] : null;

        $overlap = $this->allocationValidation->findOverlappingAllocation(
            $providerId,
            $operatorId,
            $serviceId,
            $startDate,
            $endDate,
            $excludeAllocationId,
        );

        if ($overlap instanceof Allocation) {
            throw ValidationException::withMessages([
                'start_date' => __('account.allocations.validation.date_overlap'),
            ]);
        }

        $inventoryViolation = $this->inventoryCapacity->findInventoryViolation(
            $variant,
            $capacity,
            $startDate,
            $endDate,
            (bool) ($validated['active'] ?? false),
            $allocationType,
            $excludeAllocationId,
        );

        if ($inventoryViolation !== null) {
            $message = ($inventoryViolation['reason'] ?? '') === 'missing_inventory_total'
                ? __('account.allocations.validation.inventory_not_defined')
                : __('account.allocations.validation.capacity_exceeds_inventory', [
                    'date' => $inventoryViolation['date'] !== ''
                        ? locale_date($inventoryViolation['date'])
                        : __('account.allocations.validity_open'),
                    'assigned' => number_format((int) $inventoryViolation['assigned']),
                    'limit' => number_format((int) $inventoryViolation['limit']),
                ]);

            throw ValidationException::withMessages([
                'capacity' => $message,
            ]);
        }

        return [
            'service_variant_id' => $serviceId,
            'allocation_type' => $allocationType,
            'capacity' => $capacity,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'active' => (bool) ($validated['active'] ?? false),
        ];
    }

    private function resolveProviderAccount(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($user->hasRoleForCurrentAccount('owner'), 403);

        return AccountBusinessTypeGate::assertProviderAccount($request);
    }

    private function assertApprovedOperatorRelationship(Account $provider, Account $operator): void
    {
        abort_unless(
            AccountRelationship::query()
                ->where('provider_account_id', $provider->id)
                ->where('operator_account_id', $operator->id)
                ->where('status', AccountRelationship::STATUS_APPROVED)
                ->exists(),
            404
        );
    }

    private function assertAllocationBelongsToProvider(Allocation $allocation, int $providerId): void
    {
        abort_unless((int) $allocation->provider_id === $providerId, 404);
    }

    /**
     * @param  array{services: array<int, string>, variants: array<int, string>}  $targetOptions
     * @return array{services: array<int, string>, variants: array<int, string>}
     */
    private function mergeCurrentAllocationTarget(Allocation $allocation, array $targetOptions): array
    {
        $variantId = (int) $allocation->service_variant_id;
        if (! isset($targetOptions['variants'][$variantId])) {
            $targetOptions['variants'][$variantId] = $this->allocationValidation->allocationTargetLabel($allocation);
        }

        return $targetOptions;
    }
}
