<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountRelationship;
use App\Models\Currency;
use App\Models\OperatorPriceList;
use App\Models\OperatorPackageItem;
use App\Models\OperatorPriceListItem;
use App\Services\AccountRelationshipsListingService;
use App\Services\OperatorPackageItemSelectOptions;
use App\Services\OperatorPriceListItemPricingService;
use App\Services\PriceFormatService;
use App\Support\AccountBusinessTypeGate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class AccountOperatorPriceListController extends Controller
{
    public function __construct(
        private readonly PriceFormatService $priceFormatService,
        private readonly OperatorPackageItemSelectOptions $packageItemSelectOptions,
        private readonly OperatorPriceListItemPricingService $itemPricingService,
        private readonly AccountRelationshipsListingService $relationshipsListing,
    ) {
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
            'packageItemOptions' => $this->packageItemSelectOptions->optionsForOperator($account->id),
            'priceFormatSettings' => $this->priceFormatService->resolveSettings($account->id),
            'itemPreviewUrl' => route('account.operator-price-lists.preview-item'),
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
                fn (array $item): array => $this->normalizedItemRow($item),
                $validated['items']
            ));
        });

        return redirect()
            ->route('account.operator-price-lists.index')
            ->with('status', __('account.operator_price_lists.status_created'));
    }

    public function previewItem(Request $request): JsonResponse
    {
        $account = $this->resolveCurrentAccount($request);
        AccountBusinessTypeGate::assertHasActiveType($account, 'operator');

        $validated = $request->validate([
            'operator_package_item_id' => ['required', 'integer', 'min:1'],
            'currency_id' => ['required', 'integer', Rule::exists('cat_currencies', 'id')],
            'pricing_mode' => ['nullable', Rule::in([
                OperatorPriceListItem::MODE_PERCENTAGE,
                OperatorPriceListItem::MODE_FIXED_DELTA,
                OperatorPriceListItem::MODE_FIXED_PRICE,
                '',
            ])],
            'price' => ['nullable', 'numeric'],
        ]);

        $packageItem = $this->findPackageItemForOperator(
            (int) $validated['operator_package_item_id'],
            $account->id,
        );

        $pricingMode = $this->itemPricingService->normalizeMode($validated['pricing_mode'] ?? null);
        $normalizedPrice = $this->priceFormatService->normalizeNumericInput(
            $validated['price'] ?? 0,
            $account->id,
        );

        $result = $this->itemPricingService->calculate(
            $packageItem,
            $account->id,
            (int) $validated['currency_id'],
            $pricingMode,
            (float) ($normalizedPrice ?? 0),
        );

        return response()->json($result);
    }

    public function edit(Request $request, OperatorPriceList $operatorPriceList): View
    {
        $account = $this->resolveCurrentAccount($request);
        $this->assertPriceListBelongsToAccount($operatorPriceList, $account->id);

        $operatorPriceList->load(['items.packageItem']);

        return view('account.operator-price-lists.form', [
            'account' => $account,
            'priceList' => $operatorPriceList,
            'currencies' => Currency::query()->with('lmpCurrency')->orderBy('id')->get(),
            'packageItemOptions' => $this->packageItemSelectOptions->optionsForOperator($account->id),
            'priceFormatSettings' => $this->priceFormatService->resolveSettings($account->id),
            'itemPreviewUrl' => route('account.operator-price-lists.preview-item'),
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
                fn (array $item): array => $this->normalizedItemRow($item),
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
     *      operator_package_item_id:int|string,
     *      pricing_mode?:string|null,
     *      price?:numeric-string|int|float|null
     *   }>
     * }
     */
    private function validatePayload(Request $request, int $accountId): array
    {
        $this->normalizePriceItemInputs($request, $accountId);
        normalize_request_locale_dates($request, ['valid_from', 'valid_to']);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'currency_id' => ['required', Rule::exists('cat_currencies', 'id')],
            'valid_from' => ['nullable', 'date'],
            'valid_to' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'is_active' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.operator_package_item_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('operator_package_items', 'id')->where(function ($query) use ($accountId): void {
                    $query->whereIn('operator_service_catalog_id', function ($sub) use ($accountId): void {
                        $sub->select('id')
                            ->from('operator_service_catalog')
                            ->where('operator_id', $accountId);
                    });
                }),
            ],
            'items.*.pricing_mode' => ['nullable', Rule::in([
                OperatorPriceListItem::MODE_PERCENTAGE,
                OperatorPriceListItem::MODE_FIXED_DELTA,
                OperatorPriceListItem::MODE_FIXED_PRICE,
                '',
            ])],
            'items.*.price' => ['nullable', 'numeric'],
        ]);

        $errors = [];
        $listCurrencyId = (int) $validated['currency_id'];

        foreach ($validated['items'] as $index => $item) {
            $pricingMode = $this->itemPricingService->normalizeMode($item['pricing_mode'] ?? null);

            if ($pricingMode === null) {
                continue;
            }

            if (! in_array($pricingMode, [
                OperatorPriceListItem::MODE_PERCENTAGE,
                OperatorPriceListItem::MODE_FIXED_DELTA,
                OperatorPriceListItem::MODE_FIXED_PRICE,
            ], true)) {
                $errors["items.{$index}.pricing_mode"] = __('account.operator_price_lists.validation.pricing_mode_invalid');

                continue;
            }

            if (! array_key_exists('price', $item) || $item['price'] === '' || $item['price'] === null) {
                $errors["items.{$index}.price"] = __('account.operator_price_lists.validation.price_required_for_mode');

                continue;
            }

            $price = (float) $item['price'];
            $packageItemId = (int) ($item['operator_package_item_id'] ?? 0);

            $packageItem = $this->findPackageItemForOperator($packageItemId, $accountId);
            $computed = $this->itemPricingService->calculate(
                $packageItem,
                $accountId,
                $listCurrencyId,
                $pricingMode,
                $price,
            );

            if ($pricingMode === OperatorPriceListItem::MODE_FIXED_PRICE) {
                if ($price <= 0) {
                    $errors["items.{$index}.price"] = __('account.operator_price_lists.validation.direct_price_must_be_positive');
                }

                continue;
            }

            if (! $computed['provider_cost_available']) {
                $errors["items.{$index}.pricing_mode"] = __('account.operator_price_lists.validation.only_direct_without_provider_cost');

                continue;
            }

            if ($computed['final_price'] === null) {
                $errors["items.{$index}.price"] = __('account.operator_price_lists.validation.cannot_compute_final');
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $validated;
    }

    /**
     * @param  array{operator_package_item_id:int|string, pricing_mode?:string|null, price?:mixed}  $item
     * @return array{operator_package_item_id:int, pricing_mode:string|null, price:mixed|null}
     */
    private function normalizedItemRow(array $item): array
    {
        $pricingMode = $this->itemPricingService->normalizeMode($item['pricing_mode'] ?? null);

        return [
            'operator_package_item_id' => (int) ($item['operator_package_item_id'] ?? 0),
            'pricing_mode' => $pricingMode,
            'price' => $pricingMode === null ? null : $item['price'],
        ];
    }

    private function findPackageItemForOperator(int $packageItemId, int $operatorAccountId): OperatorPackageItem
    {
        $item = OperatorPackageItem::query()
            ->whereKey($packageItemId)
            ->whereHas('catalog', function ($query) use ($operatorAccountId): void {
                $query->where('operator_id', $operatorAccountId);
            })
            ->with([
                'serviceVariant.currency.lmpCurrency',
                'serviceOffer',
            ])
            ->first();

        abort_unless($item instanceof OperatorPackageItem, 404);

        return $item;
    }

    private function resolveCurrentAccount(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($user->hasRoleForCurrentAccount('owner'), 403);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);
        AccountBusinessTypeGate::assertHasActiveType($account, 'operator');

        return $account;
    }

    private function assertPriceListBelongsToAccount(OperatorPriceList $priceList, int $accountId): void
    {
        abort_unless((int) $priceList->operator_id === $accountId, 404);
    }

    /**
     * Approved agency accounts linked to this operator (commercial relationships).
     *
     * @return array<int, string> agency_account_id => display label
     */
    private function linkedAgencyOptionsForOperatorAccount(int $operatorAccountId): array
    {
        $options = [];

        foreach ($this->relationshipsListing->forAccount($operatorAccountId, 'operator', 'agency') as $row) {
            if ($row['relationship']->status !== AccountRelationship::STATUS_APPROVED) {
                continue;
            }

            $agency = $row['counterpart'];
            $id = (int) $agency->id;
            $options[$id] = $row['counterpart_label'];
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
            $validFromStr = ($validFrom === '' || $validFrom === null) ? null : parse_date_input((string) $validFrom);
            $validToStr = ($validTo === '' || $validTo === null) ? null : parse_date_input((string) $validTo);

            if ($validFrom !== null && $validFrom !== '' && $validFromStr === null) {
                $errors["assignments.{$index}.valid_from"] = __('account.operator_price_lists.validation.date_invalid');

                continue;
            }
            if ($validTo !== null && $validTo !== '' && $validToStr === null) {
                $errors["assignments.{$index}.valid_to"] = __('account.operator_price_lists.validation.date_invalid');

                continue;
            }
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

            $pricingMode = $this->itemPricingService->normalizeMode($item['pricing_mode'] ?? null);
            if ($pricingMode === null) {
                $items[$index]['price'] = '';

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
