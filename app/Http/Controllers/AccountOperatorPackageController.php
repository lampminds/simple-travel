<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Language;
use App\Models\OperatorServiceCatalog;
use App\Services\OperatorPackageOfferCatalog;
use App\Services\Translation\TranslationService;
use App\Support\AccountBusinessTypeGate;
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
    public function __construct(private readonly OperatorPackageOfferCatalog $offerCatalog)
    {
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
            $operatorPackage->items()->delete();
            $this->syncItems($operatorPackage, $account->id, $validated['items']);
        });

        return redirect()
            ->route('account.operator-packages.index')
            ->with('status', __('account.operator_packages.status_updated'));
    }

    public function destroy(Request $request, OperatorServiceCatalog $operatorPackage): RedirectResponse
    {
        $account = $this->resolveOperatorOwnerAccount($request);
        $this->assertPackageBelongsToAccount($operatorPackage, $account->id);

        DB::transaction(function () use ($operatorPackage): void {
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
        $this->resolveOperatorOwnerAccount($request);

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
            userId: $request->user()?->id
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

        return view('account.operator-packages.form', [
            'account' => $account,
            'package' => $package,
            'languages' => $languages,
            'providerOptions' => $catalogPayload['providers'],
            'offersByProvider' => $catalogPayload['offersByProvider'],
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
     *     notes?: string|null
     * }>  $items
     */
    private function syncItems(OperatorServiceCatalog $package, int $operatorAccountId, array $items): void
    {
        foreach ($items as $row) {
            $offer = $this->offerCatalog->findEligibleOffer($operatorAccountId, (int) $row['service_offer_id']);
            abort_unless($offer !== null, 422);
            abort_unless((int) $offer->provider_id === (int) $row['provider_id'], 422);

            $variantId = $offer->service_variant_id !== null ? (int) $offer->service_variant_id : null;
            $serviceId = (int) ($offer->service_id ?? $offer->serviceVariant?->service_id ?? 0);
            abort_unless($serviceId > 0 && $serviceId === (int) $row['service_id'], 422);
            if ($variantId !== null) {
                abort_unless($variantId === (int) ($row['service_variant_id'] ?? 0), 422);
            }

            $package->items()->create([
                'service_id' => $serviceId,
                'service_variant_id' => $variantId,
                'service_offer_id' => (int) $offer->id,
                'day_number' => isset($row['day_number']) && $row['day_number'] !== '' ? (int) $row['day_number'] : null,
                'sort_order' => (int) ($row['sort_order'] ?? 9999),
                'quantity' => max(1, (int) ($row['quantity'] ?? 1)),
                'inclusion_mode' => (string) $row['inclusion_mode'],
                'notes' => $row['notes'] ?? null,
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
            'items.*.service_offer_id' => ['required', 'integer', 'min:1'],
            'items.*.service_id' => ['required', 'integer', 'min:1'],
            'items.*.service_variant_id' => ['nullable', 'integer', 'min:1'],
            'items.*.day_number' => ['nullable', 'integer', 'min:1', 'max:999'],
            'items.*.sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'items.*.inclusion_mode' => ['required', 'string', Rule::in(['included', 'optional', 'upgrade'])],
            'items.*.notes' => ['nullable', 'string', 'max:5000'],
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
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $validated;
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
