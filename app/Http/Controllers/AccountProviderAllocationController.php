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

    public function index(Request $request): View
    {
        $account = $this->resolveProviderAccount($request);

        $relationships = AccountRelationship::query()
            ->where('provider_account_id', $account->id)
            ->where('status', AccountRelationship::STATUS_APPROVED)
            ->with('operatorAccount')
            ->orderBy('id')
            ->get();

        $operatorOptions = [];
        foreach ($relationships as $relationship) {
            $operator = $relationship->operatorAccount;
            if (! $operator instanceof Account) {
                continue;
            }

            $operatorOptions[(int) $operator->id] = $operator->commercial_name
                ?? $operator->name
                ?? ('#' . $operator->id);
        }

        $selectedOperatorId = $request->integer('operator') ?: null;
        $selectedOperator = null;

        if ($selectedOperatorId === null && count($operatorOptions) === 1) {
            $selectedOperatorId = array_key_first($operatorOptions);
        }

        if ($selectedOperatorId !== null && isset($operatorOptions[$selectedOperatorId])) {
            $selectedOperator = Account::query()->find($selectedOperatorId);
            abort_unless($selectedOperator instanceof Account, 404);
            $this->assertApprovedOperatorRelationship($account, $selectedOperator);
        } else {
            $selectedOperatorId = null;
        }

        $allocationsQuery = Allocation::query()
            ->where('provider_id', $account->id)
            ->with([
                'operatorAccount',
                'service.translations.language.locale',
                'serviceVariant.translations.language.locale',
                'serviceVariant.service.translations.language.locale',
            ]);

        if ($selectedOperatorId !== null) {
            $allocationsQuery->where('operator_id', $selectedOperatorId);
        }

        $allocations = $allocationsQuery
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

        $openModal = (string) $request->query('modal', '');
        $formContext = null;

        if ($openModal === 'create') {
            if ($selectedOperator instanceof Account) {
                $formContext = $this->buildFormContext($account, $selectedOperator, null);
            } elseif ($operatorOptions !== []) {
                $formContext = [
                    'mode' => 'operator_picker',
                    'operatorOptions' => $operatorOptions,
                ];
            }
        } elseif ($openModal === 'edit') {
            $allocationId = $request->integer('allocation');
            $editAllocation = Allocation::query()
                ->whereKey($allocationId)
                ->where('provider_id', $account->id)
                ->with([
                    'operatorAccount',
                    'service.translations.language.locale',
                    'serviceVariant.translations.language.locale',
                    'serviceVariant.service.translations.language.locale',
                ])
                ->first();

            if ($editAllocation instanceof Allocation) {
                $operator = $editAllocation->operatorAccount;
                abort_unless($operator instanceof Account, 404);

                if ($selectedOperatorId === null) {
                    $selectedOperatorId = (int) $operator->id;
                    $selectedOperator = $operator;
                }

                $formContext = $this->buildFormContext($account, $operator, $editAllocation);
            }
        }

        return view('account.allocations.provider.index', [
            'account' => $account,
            'operatorOptions' => $operatorOptions,
            'selectedOperatorId' => $selectedOperatorId,
            'selectedOperator' => $selectedOperator,
            'allocations' => $allocations,
            'showOperatorColumn' => $selectedOperatorId === null,
            'formContext' => $formContext,
            'openModal' => $formContext !== null ? $openModal : '',
        ]);
    }

    public function store(Request $request, Account $operator): RedirectResponse
    {
        $account = $this->resolveProviderAccount($request);
        $this->assertApprovedOperatorRelationship($account, $operator);

        try {
            $payload = $this->validatePayload($request, (int) $account->id, (int) $operator->id);
        } catch (ValidationException $exception) {
            throw $exception->redirectTo($this->allocationIndexUrl($operator, 'create'));
        }

        Allocation::query()->create([
            'provider_id' => $account->id,
            'operator_id' => $operator->id,
            ...$payload,
        ]);

        return redirect()
            ->to($this->allocationIndexUrl($operator))
            ->with('status', __('account.allocations.status_created'));
    }

    public function update(Request $request, Allocation $allocation): RedirectResponse
    {
        $account = $this->resolveProviderAccount($request);
        $this->assertAllocationBelongsToProvider($allocation, (int) $account->id);

        $operatorId = (int) $allocation->operator_id;

        try {
            $payload = $this->validatePayload(
                $request,
                (int) $account->id,
                $operatorId,
                (int) $allocation->id,
            );
        } catch (ValidationException $exception) {
            throw $exception->redirectTo($this->allocationIndexUrl(
                $allocation->operatorAccount,
                'edit',
                $allocation,
            ));
        }

        $allocation->update($payload);

        $operator = $allocation->operatorAccount;
        abort_unless($operator instanceof Account, 404);

        return redirect()
            ->to($this->allocationIndexUrl($operator))
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
            ->to($this->allocationIndexUrl($operator))
            ->with('status', __('account.allocations.status_deleted'));
    }

    /**
     * @return array{
     *     operator: Account,
     *     allocation: Allocation|null,
     *     targetOptions: array{services: array<int, string>, variants: array<int, string>},
     *     selectedTargetKey: string|null,
     *     submitRoute: string,
     *     submitMethod: string,
     *     isEdit: bool,
     * }
     */
    private function buildFormContext(Account $account, Account $operator, ?Allocation $allocation): array
    {
        $targetOptions = $this->allocationValidation->eligibleTargetOptions((int) $account->id, (int) $operator->id);

        if ($allocation instanceof Allocation) {
            $targetOptions = $this->mergeCurrentAllocationTarget($allocation, $targetOptions);
        }

        $isEdit = $allocation instanceof Allocation;

        return [
            'mode' => $isEdit ? 'edit' : 'create',
            'operator' => $operator,
            'allocation' => $allocation,
            'targetOptions' => $targetOptions,
            'selectedTargetKey' => old(
                'target_key',
                $isEdit
                    ? $this->allocationValidation->targetKeyFromAllocation($allocation)
                    : null,
            ),
            'submitRoute' => $isEdit
                ? route('account.allocations.update', $allocation)
                : route('account.allocations.operators.store', $operator),
            'submitMethod' => $isEdit ? 'PUT' : 'POST',
            'isEdit' => $isEdit,
        ];
    }

    private function allocationIndexUrl(
        ?Account $operator,
        ?string $modal = null,
        ?Allocation $allocation = null,
    ): string {
        $params = [];

        if ($operator instanceof Account) {
            $params['operator'] = $operator->id;
        }

        if ($modal === 'create') {
            $params['modal'] = 'create';
        } elseif ($modal === 'edit' && $allocation instanceof Allocation) {
            $params['modal'] = 'edit';
            $params['allocation'] = $allocation->id;
        }

        return route('account.allocations.index', $params);
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
