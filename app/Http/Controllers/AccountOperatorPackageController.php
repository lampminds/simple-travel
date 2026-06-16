<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Language;
use App\Models\OperatorPackageConditionOverride;
use App\Models\OperatorPackageItem;
use App\Models\OperatorPackageItemConditionOverride;
use App\Models\OperatorPriceListItem;
use App\Models\OperatorServiceCatalog;
use App\Services\OperatorPackageConditionFormService;
use App\Services\OperatorPackageOfferCatalog;
use App\Services\Translation\TranslationService;
use App\Support\AccountBusinessTypeGate;
use App\Support\AiUsageContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class AccountOperatorPackageController extends Controller
{
    public function __construct(
        private readonly OperatorPackageOfferCatalog $offerCatalog,
        private readonly OperatorPackageConditionFormService $conditionFormService,
    ) {
    }

    public function index(Request $request): View
    {
        $account = $this->resolveOperatorOwnerAccount($request);

        $packages = OperatorServiceCatalog::query()
            ->where('operator_id', $account->id)
            ->with('translations.language.locale')
            ->withCount('items')
            ->orderByDesc('id')
            ->paginate(20);

        return view('account.operator-packages.index', [
            'account' => $account,
            'packages' => $packages,
        ]);
    }

    public function create(Request $request): View
    {
        $account = $this->resolveOperatorOwnerAccount($request);

        return $this->formView($account, null);
    }

    public function store(Request $request): RedirectResponse
    {
        $account = $this->resolveOperatorOwnerAccount($request);
        $validated = $this->validatePayload($request, $account->id);

        DB::transaction(function () use ($account, $validated): void {
            $package = OperatorServiceCatalog::query()->create([
                'operator_id' => $account->id,
                'status' => $validated['status'],
                'is_featured' => (bool) ($validated['is_featured'] ?? false),
                'is_public' => (bool) ($validated['is_public'] ?? false),
            ]);

            $this->syncTranslations($package, $validated['translations']);
            $this->syncItems($package, $account->id, $validated['items']);
            $this->syncPackageConditionOverrides($package, $validated['package_conditions'] ?? []);
        });

        return redirect()
            ->route('account.operator-packages.index')
            ->with('status', __('account.operator_packages.status_created'));
    }

    public function edit(Request $request, OperatorServiceCatalog $operatorPackage): View
    {
        $account = $this->resolveOperatorOwnerAccount($request);
        $this->assertPackageBelongsToAccount($operatorPackage, $account->id);

        $operatorPackage->load([
            'translations.language.locale',
            'items.serviceOffer',
            'items.service',
            'items.serviceVariant',
            'items.conditionOverrides.translations',
            'conditionOverrides.translations',
        ]);

        return $this->formView($account, $operatorPackage);
    }

    public function update(Request $request, OperatorServiceCatalog $operatorPackage): RedirectResponse
    {
        $account = $this->resolveOperatorOwnerAccount($request);
        $this->assertPackageBelongsToAccount($operatorPackage, $account->id);

        $validated = $this->validatePayload($request, $account->id);

        DB::transaction(function () use ($operatorPackage, $account, $validated): void {
            $operatorPackage->update([
                'status' => $validated['status'],
                'is_featured' => (bool) ($validated['is_featured'] ?? false),
                'is_public' => (bool) ($validated['is_public'] ?? false),
            ]);

            $this->syncTranslations($operatorPackage, $validated['translations']);
            $this->syncItems($operatorPackage, $account->id, $validated['items'], preserveExisting: true);
            $this->syncPackageConditionOverrides($operatorPackage, $validated['package_conditions'] ?? []);
        });

        return redirect()
            ->route('account.operator-packages.index')
            ->with('status', __('account.operator_packages.status_updated'));
    }

    public function destroy(Request $request, OperatorServiceCatalog $operatorPackage): RedirectResponse
    {
        $account = $this->resolveOperatorOwnerAccount($request);
        $this->assertPackageBelongsToAccount($operatorPackage, $account->id);

        $itemIds = $operatorPackage->items()->pluck('id');

        if ($itemIds->isNotEmpty()) {
            $referencedInPriceLists = OperatorPriceListItem::query()
                ->whereIn('operator_package_item_id', $itemIds)
                ->exists();

            if ($referencedInPriceLists) {
                return redirect()
                    ->route('account.operator-packages.index')
                    ->withErrors([
                        'package' => __('account.operator_packages.validation.package_in_price_list'),
                    ]);
            }
        }

        DB::transaction(function () use ($operatorPackage): void {
            $operatorPackage->conditionOverrides()->delete();
            $operatorPackage->items()->delete();
            $operatorPackage->translations()->delete();
            $operatorPackage->delete();
        });

        return redirect()
            ->route('account.operator-packages.index')
            ->with('status', __('account.operator_packages.status_deleted'));
    }

    public function translateTranslations(Request $request, TranslationService $translationService): JsonResponse
    {
        $account = $this->resolveOperatorOwnerAccount($request);
        $user = $request->user();

        $validated = $request->validate(
            [
                'source_language_id' => ['required', 'integer', Rule::exists(Language::class, 'id')],
                'translations' => ['required', 'array'],
                'translations.*.name' => ['nullable', 'string'],
                'translations.*.description' => ['nullable', 'string'],
            ],
            [],
            [
                'source_language_id' => __('account.operator_packages.validation.source_language_id'),
                'translations' => __('account.operator_packages.validation.translations'),
                'translations.*.name' => __('account.operator_packages.validation.translation_name'),
                'translations.*.description' => __('account.operator_packages.validation.translation_description'),
            ]
        );

        $result = $translationService->translateFromLanguage(
            sourceLanguageId: (int) $validated['source_language_id'],
            translationsPayload: $validated['translations'],
            userId: $user?->id,
            usageContext: $user !== null
                ? new AiUsageContext(
                    userId: (int) $user->id,
                    accountId: (int) $account->id,
                    accountTypeId: $account->account_type_id !== null ? (int) $account->account_type_id : null,
                    source: 'account.operator_packages.translate',
                )
                : null,
        );

        if (! $result['ok']) {
            return response()->json([
                'message' => $result['message'],
                'failures' => $result['failures'],
            ], 422);
        }

        return response()->json([
            'translations' => $result['translations'],
            'providers' => $result['providers'],
            'failures' => $result['failures'],
        ]);
    }

    public function itemConditions(Request $request): JsonResponse
    {
        $account = $this->resolveOperatorOwnerAccount($request);

        $validated = $request->validate([
            'service_offer_id' => ['required', 'integer', 'min:1'],
            'package_item_id' => ['nullable', 'integer', 'min:1'],
        ]);

        $offer = $this->offerCatalog->findEligibleOffer($account->id, (int) $validated['service_offer_id']);
        abort_unless($offer !== null, 404);

        $existingItem = null;
        if (! empty($validated['package_item_id'])) {
            $existingItem = OperatorPackageItem::query()
                ->whereKey((int) $validated['package_item_id'])
                ->whereHas('catalog', fn ($query) => $query->where('operator_id', $account->id))
                ->first();
        }

        return response()->json([
            'rows' => $this->conditionFormService->inheritableRowsForOffer(
                $account->id,
                (int) $validated['service_offer_id'],
                $existingItem,
            ),
        ]);
    }

    public function offers(Request $request): JsonResponse
    {
        $account = $this->resolveOperatorOwnerAccount($request);

        $validated = $request->validate([
            'provider_id' => ['required', 'integer', 'min:1'],
        ]);

        $providerId = (int) $validated['provider_id'];
        $providers = $this->offerCatalog->providerOptionsForOperator($account->id);
        abort_unless(array_key_exists($providerId, $providers), 404);

        return response()->json([
            'offers' => $this->offerCatalog->offerOptionsForProvider($account->id, $providerId),
        ]);
    }

    private function formView(Account $account, ?OperatorServiceCatalog $package): View
    {
        $languages = $this->languagesForValidation();
        $catalogPayload = $this->offerCatalog->catalogPayloadForOperator($account->id);
        $isEdit = $package !== null;

        $editItemIds = [];
        if ($package !== null) {
            $editItemIds = $package->items
                ->sortBy('sort_order')
                ->values()
                ->map(fn (OperatorPackageItem $item): int => (int) $item->id)
                ->all();
        }

        return view('account.operator-packages.form', [
            'account' => $account,
            'package' => $package,
            'languages' => $languages,
            'providerOptions' => $catalogPayload['providers'],
            'offersByProvider' => $catalogPayload['offersByProvider'],
            'packageConditions' => $isEdit
                ? $this->conditionFormService->packageOverridesForForm($package)
                : [],
            'packageTopicOptions' => $this->conditionFormService->packageLevelTopicOptions(),
            'editItemIds' => $editItemIds,
            'itemConditionsUrl' => route('account.operator-packages.item-conditions'),
            'submitRoute' => $isEdit
                ? route('account.operator-packages.update', $package)
                : route('account.operator-packages.store'),
            'submitMethod' => $isEdit ? 'PUT' : 'POST',
            'cancelRoute' => route('account.operator-packages.index'),
        ]);
    }

    /**
     * @param  array<int, array{name?: string|null, description?: string|null}>  $translationsInput
     */
    private function syncTranslations(OperatorServiceCatalog $package, array $translationsInput): void
    {
        $package->translations()->delete();

        foreach ($translationsInput as $languageId => $row) {
            $package->translations()->create([
                'language_id' => (int) $languageId,
                'name' => $row['name'] ?? null,
                'description' => $row['description'] ?? null,
            ]);
        }
    }

    /**
     * @param  list<array{
     *     provider_id: int,
     *     service_offer_id: int,
     *     service_id: int,
     *     service_variant_id?: int|null,
     *     day_number?: int|null,
     *     sort_order?: int,
     *     quantity?: int,
     *     inclusion_mode: string,
     *     notes?: string|null,
     *     id?: int|null
     * }>  $items
     */
    private function syncItems(
        OperatorServiceCatalog $package,
        int $operatorAccountId,
        array $items,
        bool $preserveExisting = false,
    ): void {
        $existingItems = $preserveExisting
            ? $package->items()->get()->keyBy('id')
            : collect();

        $keptIds = [];

        foreach ($items as $position => $row) {
            $offer = $this->offerCatalog->findEligibleOffer($operatorAccountId, (int) $row['service_offer_id']);
            abort_unless($offer !== null, 422);
            abort_unless((int) $offer->provider_id === (int) $row['provider_id'], 422);

            $variantId = (int) $offer->service_variant_id;
            abort_unless($variantId > 0 && $variantId === (int) ($row['service_variant_id'] ?? 0), 422);

            $payload = [
                'service_variant_id' => $variantId,
                'service_offer_id' => (int) $offer->id,
                'day_number' => isset($row['day_number']) && $row['day_number'] !== '' ? (int) $row['day_number'] : null,
                'sort_order' => ($position + 1) * 10,
                'quantity' => max(1, (int) ($row['quantity'] ?? 1)),
                'inclusion_mode' => (string) $row['inclusion_mode'],
                'notes' => $row['notes'] ?? null,
            ];

            $itemId = isset($row['id']) && $row['id'] !== '' ? (int) $row['id'] : null;

            if ($preserveExisting && $itemId !== null && $existingItems->has($itemId)) {
                /** @var OperatorPackageItem $item */
                $item = $existingItems->get($itemId);
                $item->update($payload);
            } else {
                if ($preserveExisting && $itemId !== null) {
                    throw ValidationException::withMessages([
                        "items.{$position}.id" => __('account.operator_packages.validation.item_id_invalid'),
                    ]);
                }

                $item = $package->items()->create($payload);
                $itemId = (int) $item->id;
            }

            $keptIds[] = $itemId;

            $this->syncItemConditionOverrides($item, is_array($row['condition_overrides'] ?? null) ? $row['condition_overrides'] : []);
        }

        if ($preserveExisting) {
            $idsToDelete = $existingItems->keys()
                ->map(fn ($id) => (int) $id)
                ->diff($keptIds);

            foreach ($idsToDelete as $deleteId) {
                if (OperatorPriceListItem::query()->where('operator_package_item_id', $deleteId)->exists()) {
                    throw ValidationException::withMessages([
                        'items' => __('account.operator_packages.validation.item_in_price_list'),
                    ]);
                }

                OperatorPackageItem::query()->whereKey($deleteId)->delete();
            }
        }
    }

    /**
     * @param  array<int|string, array{action?: string|null, translations?: array<int|string, string|null>}>  $overridesInput
     */
    private function syncItemConditionOverrides(OperatorPackageItem $item, array $overridesInput): void
    {
        $item->conditionOverrides()->delete();

        foreach ($overridesInput as $topicId => $row) {
            if (! is_array($row)) {
                continue;
            }

            $action = trim((string) ($row['action'] ?? ''));
            if ($action === '') {
                continue;
            }

            $override = $item->conditionOverrides()->create([
                'service_detail_topic_id' => (int) $topicId,
                'action' => $action,
            ]);

            $this->syncOverrideTranslations($override, $row['translations'] ?? []);
        }
    }

    /**
     * @param  list<array{service_detail_topic_id?: int|string, action?: string|null, translations?: array<int|string, string|null>}>  $rows
     */
    private function syncPackageConditionOverrides(OperatorServiceCatalog $package, array $rows): void
    {
        $package->conditionOverrides()->delete();

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $topicId = (int) ($row['service_detail_topic_id'] ?? 0);
            $action = trim((string) ($row['action'] ?? ''));
            if ($topicId < 1 || $action === '') {
                continue;
            }

            $override = $package->conditionOverrides()->create([
                'service_detail_topic_id' => $topicId,
                'action' => $action,
            ]);

            $this->syncOverrideTranslations($override, $row['translations'] ?? []);
        }
    }

    /**
     * @param  OperatorPackageConditionOverride|OperatorPackageItemConditionOverride  $override
     * @param  array<int|string, string|null>  $translationsInput
     */
    private function syncOverrideTranslations(object $override, array $translationsInput): void
    {
        foreach ($translationsInput as $languageId => $text) {
            $text = trim((string) $text);
            if ($text === '') {
                continue;
            }

            $override->translations()->create([
                'language_id' => (int) $languageId,
                'custom_text' => $text,
            ]);
        }
    }

    /**
     * @return array{
     *     status: string,
     *     is_featured: bool,
     *     is_public: bool,
     *     translations: array<int, array{name: string, description?: string|null}>,
     *     items: list<array<string, mixed>>
     * }
     */
    private function validatePayload(Request $request, int $operatorAccountId): array
    {
        $languages = $this->languagesForValidation();
        $providerIds = array_keys($this->offerCatalog->providerOptionsForOperator($operatorAccountId));

        if ($providerIds === []) {
            throw ValidationException::withMessages([
                'items' => __('account.operator_packages.no_providers'),
            ]);
        }

        $rules = [
            'status' => ['required', 'string', Rule::in(['active', 'hidden', 'paused', 'archived'])],
            'is_featured' => ['nullable', 'boolean'],
            'is_public' => ['nullable', 'boolean'],
            'translations' => ['required', 'array'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.provider_id' => ['required', 'integer', Rule::in($providerIds)],
            'items.*.id' => ['nullable', 'integer', 'min:1'],
            'items.*.service_offer_id' => ['required', 'integer', 'min:1'],
            'items.*.service_variant_id' => ['required', 'integer', 'min:1'],
            'items.*.day_number' => ['nullable', 'integer', 'min:1', 'max:999'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'items.*.inclusion_mode' => ['required', 'string', Rule::in(['included', 'optional', 'upgrade'])],
            'items.*.notes' => ['nullable', 'string', 'max:5000'],
            'items.*.condition_overrides' => ['nullable', 'array'],
            'items.*.condition_overrides.*.action' => ['nullable', 'string', Rule::in(OperatorPackageItemConditionOverride::ACTIONS)],
            'items.*.condition_overrides.*.translations' => ['nullable', 'array'],
            'items.*.condition_overrides.*.translations.*' => ['nullable', 'string'],
            'package_conditions' => ['nullable', 'array'],
            'package_conditions.*.service_detail_topic_id' => ['required', 'integer', 'min:1'],
            'package_conditions.*.action' => ['required', 'string', Rule::in(OperatorPackageConditionOverride::ACTIONS)],
            'package_conditions.*.translations' => ['nullable', 'array'],
            'package_conditions.*.translations.*' => ['nullable', 'string'],
        ];

        foreach ($languages as $language) {
            $rules["translations.{$language->id}.name"] = ['required', 'string', 'max:255'];
            $rules["translations.{$language->id}.description"] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules, [], $this->validationAttributes($languages));

        $errors = [];
        foreach ($validated['items'] as $index => $item) {
            $offer = $this->offerCatalog->findEligibleOffer(
                $operatorAccountId,
                (int) $item['service_offer_id']
            );
            if ($offer === null) {
                $errors["items.{$index}.service_offer_id"] = __('account.operator_packages.validation.offer_not_eligible');

                continue;
            }
            if ((int) $offer->provider_id !== (int) $item['provider_id']) {
                $errors["items.{$index}.provider_id"] = __('account.operator_packages.validation.provider_mismatch');
            }

            $errors = array_merge(
                $errors,
                $this->validateItemConditionOverrides(
                    $operatorAccountId,
                    (int) $item['service_offer_id'],
                    is_array($item['condition_overrides'] ?? null) ? $item['condition_overrides'] : [],
                    $index,
                ),
            );
        }

        $errors = array_merge(
            $errors,
            $this->validatePackageConditionOverrides(
                is_array($validated['package_conditions'] ?? null) ? $validated['package_conditions'] : [],
            ),
        );

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $validated;
    }

    /**
     * @param  array<int|string, array{action?: string|null, translations?: array<int|string, string|null>}>  $overrides
     * @return array<string, string>
     */
    private function validateItemConditionOverrides(
        int $operatorAccountId,
        int $offerId,
        array $overrides,
        int $itemIndex,
    ): array {
        $errors = [];
        $allowedTopics = collect($this->conditionFormService->inheritableRowsForOffer($operatorAccountId, $offerId))
            ->keyBy('topic_id');

        foreach ($overrides as $topicId => $row) {
            if (! is_array($row)) {
                continue;
            }

            $action = trim((string) ($row['action'] ?? ''));
            if ($action === '') {
                continue;
            }

            $topicKey = (int) $topicId;
            $topicRow = $allowedTopics->get($topicKey);
            if ($topicRow === null) {
                $errors["items.{$itemIndex}.condition_overrides.{$topicId}.action"] =
                    __('account.operator_packages.validation.condition_topic_invalid');

                continue;
            }

            if (! in_array($action, $topicRow['allowed_actions'], true)) {
                $errors["items.{$itemIndex}.condition_overrides.{$topicId}.action"] =
                    __('account.operator_packages.validation.condition_action_not_allowed');
            }

            if (
                $this->conditionFormService->actionRequiresText($action)
                && ! $this->conditionFormService->hasAnyCustomText(is_array($row['translations'] ?? null) ? $row['translations'] : [])
            ) {
                $errors["items.{$itemIndex}.condition_overrides.{$topicId}.translations"] =
                    __('account.operator_packages.validation.condition_text_required');
            }
        }

        return $errors;
    }

    /**
     * @param  list<array{service_detail_topic_id?: int|string, action?: string|null, translations?: array<int|string, string|null>}>  $rows
     * @return array<string, string>
     */
    private function validatePackageConditionOverrides(array $rows): array
    {
        $errors = [];
        $allowedTopics = collect($this->conditionFormService->packageLevelTopicOptions())->keyBy('topic_id');
        $usedTopicIds = [];

        foreach ($rows as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $topicId = (int) ($row['service_detail_topic_id'] ?? 0);
            $action = trim((string) ($row['action'] ?? ''));

            if ($topicId < 1 || $action === '') {
                continue;
            }

            if (isset($usedTopicIds[$topicId])) {
                $errors["package_conditions.{$index}.service_detail_topic_id"] =
                    __('account.operator_packages.validation.condition_topic_duplicate');

                continue;
            }
            $usedTopicIds[$topicId] = true;

            $topicRow = $allowedTopics->get($topicId);
            if ($topicRow === null) {
                $errors["package_conditions.{$index}.service_detail_topic_id"] =
                    __('account.operator_packages.validation.condition_topic_invalid');

                continue;
            }

            if (! in_array($action, $topicRow['allowed_actions'], true)) {
                $errors["package_conditions.{$index}.action"] =
                    __('account.operator_packages.validation.condition_action_not_allowed');
            }

            if (
                $this->conditionFormService->actionRequiresText($action)
                && ! $this->conditionFormService->hasAnyCustomText(is_array($row['translations'] ?? null) ? $row['translations'] : [])
            ) {
                $errors["package_conditions.{$index}.translations"] =
                    __('account.operator_packages.validation.condition_text_required');
            }
        }

        return $errors;
    }

    /**
     * @param  Collection<int, Language>  $languages
     * @return array<string, string>
     */
    private function validationAttributes(Collection $languages): array
    {
        $attrs = [
            'status' => __('account.operator_packages.fields.status'),
            'items' => __('account.operator_packages.items_heading'),
        ];

        foreach ($languages as $language) {
            $id = (int) $language->id;
            $localeLabel = $language->display_name;
            $attrs["translations.{$id}.name"] = __('account.operator_packages.fields.name').' ('.$localeLabel.')';
            $attrs["translations.{$id}.description"] = __('account.operator_packages.fields.description').' ('.$localeLabel.')';
        }

        return $attrs;
    }

    /**
     * @return Collection<int, Language>
     */
    private function languagesForValidation(): Collection
    {
        return Language::query()
            ->with('locale')
            ->get()
            ->values();
    }

    private function resolveOperatorOwnerAccount(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($user->hasRoleForCurrentAccount('owner'), 403);

        return AccountBusinessTypeGate::assertOperatorAccount($request);
    }

    private function assertPackageBelongsToAccount(OperatorServiceCatalog $package, int $accountId): void
    {
        abort_unless((int) $package->operator_id === $accountId, 404);
    }
}
