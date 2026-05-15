<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Currency;
use App\Models\OperatorPriceList;
use App\Models\OperatorServiceCatalog;
use App\Services\PriceFormatService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class AccountOperatorPriceListController extends Controller
{
    public function __construct(private readonly PriceFormatService $priceFormatService)
    {
    }

    public function index(Request $request): View
    {
        $account = $this->resolveCurrentAccount($request);

        $priceLists = OperatorPriceList::query()
            ->where('operator_id', $account->id)
            ->with('currency.lmpCurrency')
            ->withCount('items')
            ->withCount('assignments as agency_assignments_count')
            ->orderByDesc('id')
            ->paginate(20);

        return view('account.operator-price-lists.index', [
            'account' => $account,
            'priceLists' => $priceLists,
        ]);
    }

    public function create(Request $request): View
    {
        $account = $this->resolveCurrentAccount($request);

        return view('account.operator-price-lists.form', [
            'account' => $account,
            'priceList' => null,
            'currencies' => Currency::query()->with('lmpCurrency')->orderBy('id')->get(),
            'catalogOptions' => $this->catalogOptionsForOperatorAccount($account->id),
            'priceFormatSettings' => $this->priceFormatService->resolveSettings($account->id),
            'submitRoute' => route('account.operator-price-lists.store'),
            'submitMethod' => 'POST',
            'cancelRoute' => route('account.operator-price-lists.index'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $account = $this->resolveCurrentAccount($request);
        $validated = $this->validatePayload($request, $account->id);

        DB::transaction(function () use ($account, $validated): void {
            $priceList = OperatorPriceList::query()->create([
                'operator_id' => $account->id,
                'name' => $validated['name'],
                'currency_id' => (int) $validated['currency_id'],
                'valid_from' => $validated['valid_from'] ?? null,
                'valid_to' => $validated['valid_to'] ?? null,
                'is_active' => (bool) ($validated['is_active'] ?? false),
            ]);

            $priceList->items()->createMany(array_map(
                fn (array $item): array => [
                    'operator_service_catalog_id' => (int) $item['operator_service_catalog_id'],
                    'pricing_mode' => (string) $item['pricing_mode'],
                    'price' => $item['price'],
                ],
                $validated['items']
            ));
        });

        return redirect()
            ->route('account.operator-price-lists.index')
            ->with('status', __('account.operator_price_lists.status_created'));
    }

    public function edit(Request $request, OperatorPriceList $operatorPriceList): View
    {
        $account = $this->resolveCurrentAccount($request);
        $this->assertPriceListBelongsToAccount($operatorPriceList, $account->id);

        $operatorPriceList->load('items');

        return view('account.operator-price-lists.form', [
            'account' => $account,
            'priceList' => $operatorPriceList,
            'currencies' => Currency::query()->with('lmpCurrency')->orderBy('id')->get(),
            'catalogOptions' => $this->catalogOptionsForOperatorAccount($account->id),
            'priceFormatSettings' => $this->priceFormatService->resolveSettings($account->id),
            'submitRoute' => route('account.operator-price-lists.update', $operatorPriceList),
            'submitMethod' => 'PUT',
            'cancelRoute' => route('account.operator-price-lists.index'),
        ]);
    }

    public function editAssignments(Request $request, OperatorPriceList $operatorPriceList): View
    {
        $account = $this->resolveCurrentAccount($request);
        $this->assertPriceListBelongsToAccount($operatorPriceList, $account->id);

        $operatorPriceList->load([
            'assignments' => function ($query): void {
                $query->orderBy('id');
            },
        ]);

        $agencyOptions = $this->linkedAgencyOptionsForOperatorAccount($account->id);

        return view('account.operator-price-lists.assignments', [
            'account' => $account,
            'priceList' => $operatorPriceList,
            'agencyOptions' => $agencyOptions,
            'priceFormatSettings' => $this->priceFormatService->resolveSettings($account->id),
        ]);
    }

    public function updateAssignments(Request $request, OperatorPriceList $operatorPriceList): RedirectResponse
    {
        $account = $this->resolveCurrentAccount($request);
        $this->assertPriceListBelongsToAccount($operatorPriceList, $account->id);

        $allowedAgencyIds = array_keys($this->linkedAgencyOptionsForOperatorAccount($account->id));
        $assignmentRows = $this->validateAndNormalizeAssignments($request, $allowedAgencyIds, $account->id);

        DB::transaction(function () use ($operatorPriceList, $assignmentRows): void {
            $this->persistAgencyAssignments($operatorPriceList, $assignmentRows);
        });

        return redirect()
            ->route('account.operator-price-lists.assignments.edit', $operatorPriceList)
            ->with('status', __('account.operator_price_lists.assignments_status_updated'));
    }

    public function update(Request $request, OperatorPriceList $operatorPriceList): RedirectResponse
    {
        $account = $this->resolveCurrentAccount($request);
        $this->assertPriceListBelongsToAccount($operatorPriceList, $account->id);
        $validated = $this->validatePayload($request, $account->id);

        DB::transaction(function () use ($operatorPriceList, $validated): void {
            $operatorPriceList->update([
                'name' => $validated['name'],
                'currency_id' => (int) $validated['currency_id'],
                'valid_from' => $validated['valid_from'] ?? null,
                'valid_to' => $validated['valid_to'] ?? null,
                'is_active' => (bool) ($validated['is_active'] ?? false),
            ]);

            $operatorPriceList->items()->delete();
            $operatorPriceList->items()->createMany(array_map(
                fn (array $item): array => [
                    'operator_service_catalog_id' => (int) $item['operator_service_catalog_id'],
                    'pricing_mode' => (string) $item['pricing_mode'],
                    'price' => $item['price'],
                ],
                $validated['items']
            ));
        });

        return redirect()
            ->route('account.operator-price-lists.index')
            ->with('status', __('account.operator_price_lists.status_updated'));
    }

    public function destroy(Request $request, OperatorPriceList $operatorPriceList): RedirectResponse
    {
        $account = $this->resolveCurrentAccount($request);
        $this->assertPriceListBelongsToAccount($operatorPriceList, $account->id);

        DB::transaction(function () use ($operatorPriceList): void {
            $operatorPriceList->assignments()->delete();
            $operatorPriceList->items()->delete();
            $operatorPriceList->delete();
        });

        return redirect()
            ->route('account.operator-price-lists.index')
            ->with('status', __('account.operator_price_lists.status_deleted'));
    }

    /**
     * @return array{
     *   name:string,
     *   currency_id:int|string,
     *   valid_from?:string|null,
     *   valid_to?:string|null,
     *   is_active?:bool|string|int|null,
     *   items:array<int, array{
     *      operator_service_catalog_id:int|string,
     *      pricing_mode:string,
     *      price:numeric-string|int|float
     *   }>
     * }
     */
    private function validatePayload(Request $request, int $accountId): array
    {
        $this->normalizePriceItemInputs($request, $accountId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'currency_id' => ['required', Rule::exists('cat_currencies', 'id')],
            'valid_from' => ['nullable', 'date'],
            'valid_to' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'is_active' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.operator_service_catalog_id' => [
                'required',
                'integer',
                Rule::exists('operator_service_catalog', 'id')->where(function ($query) use ($accountId) {
                    $query->where('operator_id', $accountId);
                }),
            ],
            'items.*.pricing_mode' => ['required', Rule::in(['fixed', 'percentage'])],
            'items.*.price' => ['required', 'numeric'],
        ]);

        $errors = [];
        foreach ($validated['items'] as $index => $item) {
            $pricingMode = (string) ($item['pricing_mode'] ?? '');
            $price = (float) ($item['price'] ?? 0);

            if ($pricingMode === 'fixed' && $price < 0) {
                $errors["items.{$index}.price"] = __('account.operator_price_lists.validation.fixed_amount_must_be_non_negative');
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $validated;
    }

    /**
     * @return array<int, string>
     */
    private function catalogOptionsForOperatorAccount(int $operatorAccountId): array
    {
        return OperatorServiceCatalog::query()
            ->where('operator_id', $operatorAccountId)
            ->with(['provider', 'service', 'serviceVariant'])
            ->orderBy('id')
            ->get()
            ->mapWithKeys(function (OperatorServiceCatalog $row): array {
                $provider = trim($row->provider?->commercial_name ?? $row->provider?->name ?? '');
                $service = trim($row->service?->name ?? '');
                $sku = trim((string) ($row->serviceVariant?->sku ?? ''));
                $parts = array_filter([$provider !== '' ? $provider : null, $service !== '' ? $service : null, $sku !== '' ? $sku : null]);

                return [$row->id => implode(' — ', $parts) ?: ('#'.$row->id)];
            })
            ->all();
    }

    private function resolveCurrentAccount(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($user->hasRoleForCurrentAccount('owner'), 403);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        return $account;
    }

    private function assertPriceListBelongsToAccount(OperatorPriceList $priceList, int $accountId): void
    {
        abort_unless((int) $priceList->operator_id === $accountId, 404);
    }

    /**
     * Partner accounts (distinct providers from this operator's service catalogue) allowed as assignment targets.
     *
     * @return array<int, string> agency_account_id => display label
     */
    private function linkedAgencyOptionsForOperatorAccount(int $operatorAccountId): array
    {
        $rows = OperatorServiceCatalog::query()
            ->where('operator_id', $operatorAccountId)
            ->with('provider')
            ->orderBy('provider_id')
            ->get()
            ->unique('provider_id');

        $options = [];
        foreach ($rows as $row) {
            $provider = $row->provider;
            if ($provider === null) {
                continue;
            }
            $id = (int) $provider->id;
            $label = $provider->commercial_name ?? $provider->name ?? $provider->nick ?? ('#'.$id);
            $options[$id] = (string) $label;
        }

        return $options;
    }

    /**
     * @param  array<int>  $allowedAgencyIds
     * @return array<int, array{
     *     agency_account_id:int,
     *     adjustment_type:string,
     *     adjustment_value: float|null,
     *     valid_from: string|null,
     *     valid_to: string|null,
     *     is_active: bool
     * }>
     */
    private function validateAndNormalizeAssignments(Request $request, array $allowedAgencyIds, int $accountId): array
    {
        if ($allowedAgencyIds === []) {
            return [];
        }

        $raw = $request->input('assignments', []);
        if (! is_array($raw)) {
            return [];
        }

        $allowedLookup = array_fill_keys($allowedAgencyIds, true);
        $errors = [];
        $out = [];

        $agencyIdByIndex = [];
        foreach ($raw as $index => $row) {
            if (! is_array($row)) {
                continue;
            }
            $aid = (int) ($row['agency_account_id'] ?? 0);
            if ($aid > 0) {
                $agencyIdByIndex[$index] = $aid;
            }
        }
        $usageCounts = array_count_values($agencyIdByIndex);
        foreach ($agencyIdByIndex as $index => $aid) {
            if (($usageCounts[$aid] ?? 0) > 1) {
                $errors["assignments.{$index}.agency_account_id"] = __('account.operator_price_lists.validation.agency_duplicate');
            }
        }

        foreach ($raw as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $agencyId = (int) ($row['agency_account_id'] ?? 0);
            if ($agencyId <= 0) {
                continue;
            }

            if (! isset($allowedLookup[$agencyId])) {
                $errors["assignments.{$index}.agency_account_id"] = __('account.operator_price_lists.validation.agency_not_linked');

                continue;
            }

            if (isset($errors["assignments.{$index}.agency_account_id"])) {
                continue;
            }

            $adjustmentType = (string) ($row['adjustment_type'] ?? 'none');
            if (! in_array($adjustmentType, ['none', 'percentage', 'fixed'], true)) {
                $errors["assignments.{$index}.adjustment_type"] = __('account.operator_price_lists.validation.adjustment_type_invalid');

                continue;
            }

            $adjustmentValue = $row['adjustment_value'] ?? null;
            if ($adjustmentType === 'none') {
                $adjustmentValue = null;
            } elseif ($adjustmentValue === '' || $adjustmentValue === null) {
                $errors["assignments.{$index}.adjustment_value"] = __('account.operator_price_lists.validation.adjustment_value_required');

                continue;
            }

            if ($adjustmentType !== 'none') {
                $normalizedAdjustmentValue = $this->priceFormatService->normalizeNumericInput($adjustmentValue, $accountId);
                if ($normalizedAdjustmentValue === null) {
                    $errors["assignments.{$index}.adjustment_value"] = __('account.operator_price_lists.validation.adjustment_value_numeric');

                    continue;
                }
                $adjustmentValue = $normalizedAdjustmentValue;
            }

            $validFrom = $row['valid_from'] ?? null;
            $validTo = $row['valid_to'] ?? null;
            $validFromStr = ($validFrom === '' || $validFrom === null) ? null : (string) $validFrom;
            $validToStr = ($validTo === '' || $validTo === null) ? null : (string) $validTo;

            if ($validFromStr !== null && strtotime($validFromStr) === false) {
                $errors["assignments.{$index}.valid_from"] = __('account.operator_price_lists.validation.date_invalid');

                continue;
            }
            if ($validToStr !== null && strtotime($validToStr) === false) {
                $errors["assignments.{$index}.valid_to"] = __('account.operator_price_lists.validation.date_invalid');

                continue;
            }
            if ($validFromStr !== null && $validToStr !== null && $validToStr < $validFromStr) {
                $errors["assignments.{$index}.valid_to"] = __('account.operator_price_lists.validation.valid_to_after_from');

                continue;
            }

            $out[] = [
                'agency_account_id' => $agencyId,
                'adjustment_type' => $adjustmentType,
                'adjustment_value' => $adjustmentValue === null ? null : (float) $adjustmentValue,
                'valid_from' => $validFromStr,
                'valid_to' => $validToStr,
                'is_active' => filter_var($row['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ];
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $out;
    }

    /**
     * @param  array<int, array<string, mixed>>  $assignmentRows
     */
    private function persistAgencyAssignments(OperatorPriceList $priceList, array $assignmentRows): void
    {
        $priceList->assignments()->delete();

        if ($assignmentRows === []) {
            return;
        }

        foreach ($assignmentRows as $row) {
            $priceList->assignments()->create([
                'agency_id' => (int) $row['agency_account_id'],
                'adjustment_type' => (string) $row['adjustment_type'],
                'adjustment_value' => $row['adjustment_value'],
                'valid_from' => $row['valid_from'],
                'valid_to' => $row['valid_to'],
                'is_active' => (bool) $row['is_active'],
            ]);
        }
    }

    private function normalizePriceItemInputs(Request $request, int $accountId): void
    {
        $items = $request->input('items');
        if (! is_array($items)) {
            return;
        }

        foreach ($items as $index => $item) {
            if (! is_array($item) || ! array_key_exists('price', $item)) {
                continue;
            }

            $normalized = $this->priceFormatService->normalizeNumericInput($item['price'], $accountId);
            if ($normalized === null) {
                continue;
            }

            $items[$index]['price'] = $normalized;
        }

        $request->merge(['items' => $items]);
    }
}
