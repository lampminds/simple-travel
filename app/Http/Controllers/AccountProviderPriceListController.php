<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountRelationship;
use App\Models\Currency;
use App\Models\PriceList;
use App\Models\Service;
use App\Models\ServiceVariant;
use App\Models\User;
use App\Services\PriceFormatService;
use App\Services\ProviderPriceListActiveAssignmentService;
use App\Services\ProviderPriceListChangeNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class AccountProviderPriceListController extends Controller
{
    public function __construct(
        private readonly PriceFormatService $priceFormatService,
        private readonly ProviderPriceListActiveAssignmentService $activeAssignments,
        private readonly ProviderPriceListChangeNotificationService $changeNotifier,
    ) {
    }

    public function index(Request $request): View
    {
        $account = $this->resolveCurrentAccount($request);

        $priceLists = PriceList::query()
            ->where('provider_id', $account->id)
            ->with('currency.lmpCurrency')
            ->withCount('items')
            ->withCount('assignments as operator_assignments_count')
            ->orderByDesc('id')
            ->paginate(20);

        return view('account.provider-price-lists.index', [
            'account' => $account,
            'priceLists' => $priceLists,
        ]);
    }

    public function create(Request $request): View
    {
        $account = $this->resolveCurrentAccount($request);

        return view('account.provider-price-lists.form', array_merge(
            $this->formSharedViewData($account),
            [
                'priceList' => null,
                'submitRoute' => route('account.provider-price-lists.store'),
                'submitMethod' => 'POST',
                'cancelRoute' => route('account.provider-price-lists.index'),
            ],
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $account = $this->resolveCurrentAccount($request);
        $validated = $this->validatePayload($request, $account->id);

        DB::transaction(function () use ($account, $validated): void {
            $priceList = PriceList::query()->create([
                'provider_id' => $account->id,
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
            ->route('account.provider-price-lists.index')
            ->with('status', __('account.price_lists.status_created'));
    }

    public function edit(Request $request, PriceList $priceList): View
    {
        $account = $this->resolveCurrentAccount($request);
        $this->assertPriceListBelongsToAccount($priceList, $account->id);

        $priceList->load(['items', 'assignments.operator']);

        $requiresNotificationTab = $this->activeAssignments->hasActiveOperatorAssignments($priceList);

        return view('account.provider-price-lists.form', array_merge(
            $this->formSharedViewData($account),
            [
                'priceList' => $priceList,
                'requiresNotificationTab' => $requiresNotificationTab,
                'activeOperatorLabels' => $requiresNotificationTab
                    ? $this->activeAssignments->activeOperatorLabels($priceList)
                    : [],
                'submitRoute' => route('account.provider-price-lists.update', $priceList),
                'submitMethod' => 'PUT',
                'cancelRoute' => route('account.provider-price-lists.index'),
            ],
        ));
    }

    public function editAssignments(Request $request, PriceList $priceList): View
    {
        $account = $this->resolveCurrentAccount($request);
        $this->assertPriceListBelongsToAccount($priceList, $account->id);

        $priceList->load([
            'assignments' => function ($query): void {
                $query->orderBy('id');
            },
        ]);

        $operatorOptions = $this->linkedOperatorOptionsForProviderAccount($account->id);

        return view('account.provider-price-lists.assignments', [
            'account' => $account,
            'priceList' => $priceList,
            'operatorOptions' => $operatorOptions,
            'priceFormatSettings' => $this->priceFormatService->resolveSettings($account->id),
        ]);
    }

    public function updateAssignments(Request $request, PriceList $priceList): RedirectResponse
    {
        $account = $this->resolveCurrentAccount($request);
        $this->assertPriceListBelongsToAccount($priceList, $account->id);

        $allowedOperatorIds = array_keys($this->linkedOperatorOptionsForProviderAccount($account->id));
        $assignmentRows = $this->validateAndNormalizeAssignments($request, $allowedOperatorIds, $account->id);

        DB::transaction(function () use ($priceList, $assignmentRows): void {
            $this->persistOperatorAssignments($priceList, $assignmentRows);
        });

        return redirect()
            ->route('account.provider-price-lists.assignments.edit', $priceList)
            ->with('status', __('account.price_lists.assignments_status_updated'));
    }

    public function update(Request $request, PriceList $priceList): RedirectResponse
    {
        $account = $this->resolveCurrentAccount($request);
        $this->assertPriceListBelongsToAccount($priceList, $account->id);

        $requiresNotificationTab = $this->activeAssignments->hasActiveOperatorAssignments($priceList);
        $validated = $this->validatePayload($request, $account->id, $requiresNotificationTab);

        DB::transaction(function () use ($priceList, $validated): void {
            $priceList->update([
                'name' => $validated['name'],
                'currency_id' => (int) $validated['currency_id'],
                'valid_from' => $validated['valid_from'] ?? null,
                'valid_to' => $validated['valid_to'] ?? null,
                'is_active' => (bool) ($validated['is_active'] ?? false),
            ]);

            $priceList->items()->delete();
            $priceList->items()->createMany(array_map(
                fn (array $item): array => $this->normalizedItemRow($item),
                $validated['items']
            ));
        });

        if ($requiresNotificationTab) {
            $user = $request->user();
            abort_unless($user instanceof User, 401);

            $this->changeNotifier->notifyIfRequested(
                priceList: $priceList->fresh(),
                providerAccount: $account,
                actingUser: $user,
                notificationChoice: (string) $validated['notification_choice'],
                customMessage: isset($validated['notification_message'])
                    ? (string) $validated['notification_message']
                    : null,
                sendEmail: (bool) ($validated['notification_send_email'] ?? false),
                ccActingUser: (bool) ($validated['notification_cc_me'] ?? false),
            );
        }

        return redirect()
            ->route('account.provider-price-lists.index')
            ->with('status', __('account.price_lists.status_updated'));
    }

    public function destroy(Request $request, PriceList $priceList): RedirectResponse
    {
        $account = $this->resolveCurrentAccount($request);
        $this->assertPriceListBelongsToAccount($priceList, $account->id);

        DB::transaction(function () use ($priceList): void {
            $priceList->assignments()->delete();
            $priceList->items()->delete();
            $priceList->delete();
        });

        return redirect()
            ->route('account.provider-price-lists.index')
            ->with('status', __('account.price_lists.status_deleted'));
    }

    /**
     * @return array{
     *   name:string,
     *   currency_id:int|string,
     *   valid_from?:string|null,
     *   valid_to?:string|null,
     *   is_active?:bool|string|int|null,
     *   items:array<int, array{
     *      service_id?:int|string|null,
     *      service_variant_id?:int|string|null,
     *      pricing_mode:string,
     *      price:numeric-string|int|float
     *   }>
     * }
     */
    private function validatePayload(Request $request, int $accountId, bool $requiresNotificationTab = false): array
    {
        $this->normalizePriceItemInputs($request, $accountId);
        normalize_request_locale_dates($request, ['valid_from', 'valid_to']);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'currency_id' => ['required', Rule::exists('cat_currencies', 'id')],
            'valid_from' => ['nullable', 'date'],
            'valid_to' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'is_active' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_variant_id' => [
                'required',
                'integer',
                Rule::exists('service_variants', 'id')->where(function ($query) use ($accountId) {
                    $query->whereExists(function ($subQuery) use ($accountId) {
                        $subQuery->selectRaw('1')
                            ->from('services')
                            ->whereColumn('services.id', 'service_variants.service_id')
                            ->where('services.account_id', $accountId);
                    });
                }),
            ],
            'items.*.pricing_mode' => ['nullable', Rule::in(['fixed', 'percentage', ''])],
            'items.*.price' => ['nullable', 'numeric'],
        ];

        if ($requiresNotificationTab) {
            $rules['notification_choice'] = ['required', Rule::in(['notify', 'dont_notify'])];
            $rules['notification_message'] = ['nullable', 'string', 'max:4000'];
            $rules['notification_send_email'] = ['nullable', 'boolean'];
            $rules['notification_cc_me'] = ['nullable', 'boolean'];
        }

        $validated = $request->validate($rules, [
            'notification_choice.required' => __('account.price_lists.validation.notification_choice_required'),
            'notification_choice.in' => __('account.price_lists.validation.notification_choice_required'),
        ]);

        $errors = [];
        foreach ($validated['items'] as $index => $item) {
            $vid = (int) ($item['service_variant_id'] ?? 0);

            if ($vid <= 0) {
                $errors["items.{$index}.service_variant_id"] = __('account.price_lists.validation.variant_required');
            }

            $pricingMode = (string) ($item['pricing_mode'] ?? '');

            if ($pricingMode === '') {
                continue;
            }

            if (! in_array($pricingMode, ['fixed', 'percentage'], true)) {
                $errors["items.{$index}.pricing_mode"] = __('account.price_lists.validation.pricing_mode_invalid');

                continue;
            }

            if (! array_key_exists('price', $item) || $item['price'] === '' || $item['price'] === null) {
                $errors["items.{$index}.price"] = __('account.price_lists.validation.price_required_for_mode');

                continue;
            }

            $price = (float) $item['price'];

            if ($pricingMode === 'fixed' && $price < 0) {
                $errors["items.{$index}.price"] = __('account.price_lists.validation.fixed_amount_must_be_non_negative');
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $validated;
    }

    /**
     * @return array<string, mixed>
     */
    private function formSharedViewData(Account $account): array
    {
        $currencies = Currency::query()->with('lmpCurrency')->orderBy('id')->get();

        return [
            'account' => $account,
            'currencies' => $currencies,
            'serviceVariantOptions' => $this->serviceVariantOptionsForAccount($account->id),
            'priceFormatSettings' => $this->priceFormatService->resolveSettings($account->id),
            'variantBasePrices' => $this->variantBasePricesForAccount($account->id),
            'currencyCodesById' => $currencies->mapWithKeys(
                fn (Currency $currency): array => [$currency->id => $currency->currency_code],
            )->all(),
        ];
    }

    /**
     * @return array<int, array{amount: float|null}>
     */
    private function variantBasePricesForAccount(int $accountId): array
    {
        return ServiceVariant::query()
            ->whereHas('service', fn ($query) => $query->where('account_id', $accountId))
            ->get(['id', 'base_price'])
            ->mapWithKeys(fn (ServiceVariant $variant): array => [
                $variant->id => [
                    'amount' => $variant->base_price !== null ? (float) $variant->base_price : null,
                ],
            ])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function serviceVariantOptionsForAccount(int $accountId): array
    {
        return ServiceVariant::query()
            ->whereHas('service', fn ($query) => $query->where('account_id', $accountId))
            ->with(['service.translations.language.locale'])
            ->orderBy('service_id')
            ->orderBy('sku')
            ->get()
            ->mapWithKeys(function (ServiceVariant $variant): array {
                $serviceName = trim($variant->service?->name ?? '');
                $sku = trim((string) $variant->sku);

                $serviceChunk = $serviceName !== '' ? $serviceName : ('Service #'.$variant->service_id);
                $skuChunk = $sku !== '' ? $sku : ('Variant #'.$variant->id);

                return [$variant->id => $serviceChunk.' — '.$skuChunk];
            })
            ->all();
    }

    /**
     * @param  array{service_variant_id:int|string, pricing_mode?:string|null, price:mixed}  $item
     * @return array{service_variant_id:int, pricing_mode:string|null, price:mixed|null}
     */
    private function normalizedItemRow(array $item): array
    {
        $pricingMode = (string) ($item['pricing_mode'] ?? '');

        return [
            'service_variant_id' => (int) ($item['service_variant_id'] ?? 0),
            'pricing_mode' => $pricingMode === '' ? null : $pricingMode,
            'price' => $pricingMode === '' ? null : $item['price'],
        ];
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

    private function assertPriceListBelongsToAccount(PriceList $priceList, int $accountId): void
    {
        abort_unless((int) $priceList->provider_id === $accountId, 404);
    }

    /**
     * Approved operators linked to this account as provider (for price list assignments).
     *
     * @return array<int, string> operator_account_id => display label
     */
    private function linkedOperatorOptionsForProviderAccount(int $providerAccountId): array
    {
        $relationships = AccountRelationship::query()
            ->where('provider_account_id', $providerAccountId)
            ->where('status', AccountRelationship::STATUS_APPROVED)
            ->with('operatorAccount')
            ->orderBy('id')
            ->get();

        $options = [];
        foreach ($relationships as $relationship) {
            $operator = $relationship->operatorAccount;
            if ($operator === null) {
                continue;
            }
            $id = (int) $operator->id;
            $label = $operator->commercial_name ?? $operator->name ?? $operator->nick ?? ('#'.$id);
            $options[$id] = (string) $label;
        }

        return $options;
    }

    /**
     * @param  array<int>  $allowedOperatorIds
     * @return array<int, array{
     *     operator_account_id:int,
     *     adjustment_type:string,
     *     adjustment_value: float|null,
     *     valid_from: string|null,
     *     valid_to: string|null,
     *     is_active: bool
     * }>
     */
    private function validateAndNormalizeAssignments(Request $request, array $allowedOperatorIds, int $accountId): array
    {
        if ($allowedOperatorIds === []) {
            return [];
        }

        $raw = $request->input('assignments', []);
        if (! is_array($raw)) {
            return [];
        }

        $allowedLookup = array_fill_keys($allowedOperatorIds, true);
        $errors = [];
        $out = [];

        $operatorIdByIndex = [];
        foreach ($raw as $index => $row) {
            if (! is_array($row)) {
                continue;
            }
            $oid = (int) ($row['operator_account_id'] ?? 0);
            if ($oid > 0) {
                $operatorIdByIndex[$index] = $oid;
            }
        }
        $usageCounts = array_count_values($operatorIdByIndex);
        foreach ($operatorIdByIndex as $index => $oid) {
            if (($usageCounts[$oid] ?? 0) > 1) {
                $errors["assignments.{$index}.operator_account_id"] = __('account.price_lists.validation.operator_duplicate');
            }
        }

        foreach ($raw as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $operatorId = (int) ($row['operator_account_id'] ?? 0);
            if ($operatorId <= 0) {
                continue;
            }

            if (! isset($allowedLookup[$operatorId])) {
                $errors["assignments.{$index}.operator_account_id"] = __('account.price_lists.validation.operator_not_linked');

                continue;
            }

            if (isset($errors["assignments.{$index}.operator_account_id"])) {
                continue;
            }

            $adjustmentType = (string) ($row['adjustment_type'] ?? 'none');
            if (! in_array($adjustmentType, ['none', 'percentage', 'fixed'], true)) {
                $errors["assignments.{$index}.adjustment_type"] = __('account.price_lists.validation.adjustment_type_invalid');

                continue;
            }

            $adjustmentValue = $row['adjustment_value'] ?? null;
            if ($adjustmentType === 'none') {
                $adjustmentValue = null;
            } elseif ($adjustmentValue === '' || $adjustmentValue === null) {
                $errors["assignments.{$index}.adjustment_value"] = __('account.price_lists.validation.adjustment_value_required');

                continue;
            }

            if ($adjustmentType !== 'none') {
                $normalizedAdjustmentValue = $this->priceFormatService->normalizeNumericInput($adjustmentValue, $accountId);
                if ($normalizedAdjustmentValue === null) {
                    $errors["assignments.{$index}.adjustment_value"] = __('account.price_lists.validation.adjustment_value_numeric');

                    continue;
                }
                $adjustmentValue = $normalizedAdjustmentValue;
            }

            $validFrom = $row['valid_from'] ?? null;
            $validTo = $row['valid_to'] ?? null;
            $validFromStr = ($validFrom === '' || $validFrom === null) ? null : parse_date_input((string) $validFrom);
            $validToStr = ($validTo === '' || $validTo === null) ? null : parse_date_input((string) $validTo);

            if ($validFrom !== null && $validFrom !== '' && $validFromStr === null) {
                $errors["assignments.{$index}.valid_from"] = __('account.price_lists.validation.date_invalid');

                continue;
            }
            if ($validTo !== null && $validTo !== '' && $validToStr === null) {
                $errors["assignments.{$index}.valid_to"] = __('account.price_lists.validation.date_invalid');

                continue;
            }
            if ($validFromStr !== null && strtotime($validFromStr) === false) {
                $errors["assignments.{$index}.valid_from"] = __('account.price_lists.validation.date_invalid');

                continue;
            }
            if ($validToStr !== null && strtotime($validToStr) === false) {
                $errors["assignments.{$index}.valid_to"] = __('account.price_lists.validation.date_invalid');

                continue;
            }
            if ($validFromStr !== null && $validToStr !== null && $validToStr < $validFromStr) {
                $errors["assignments.{$index}.valid_to"] = __('account.price_lists.validation.valid_to_after_from');

                continue;
            }

            $out[] = [
                'operator_account_id' => $operatorId,
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
    private function persistOperatorAssignments(PriceList $priceList, array $assignmentRows): void
    {
        $priceList->assignments()->delete();

        if ($assignmentRows === []) {
            return;
        }

        foreach ($assignmentRows as $row) {
            $priceList->assignments()->create([
                'operator_id' => (int) $row['operator_account_id'],
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
