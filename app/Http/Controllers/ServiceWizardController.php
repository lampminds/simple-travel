<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Language;
use App\Models\LmpCity;
use App\Models\Service;
use App\Models\ServiceType;
use App\Support\AccountBusinessTypeGate;
use App\Support\ServiceWizardSkipsVariantsStep;
use App\Support\ServiceWizardStepEight;
use Illuminate\Support\Collection;
use App\Services\Translation\TranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceWizardController extends Controller
{
    /** Minimum characters before city search runs (large city table). */
    private const MIN_CITY_SEARCH_LENGTH = 4;

    /** Cap matching rows returned to keep responses fast and the list usable. */
    private const MAX_CITY_SEARCH_RESULTS = 2000;

    public function createStepOne(Request $request, ServiceType $serviceType): View
    {
        $this->assertProviderAccount($request);

        return $this->renderStepOne($request, $serviceType, null);
    }

    public function editStepOne(Request $request, ServiceType $serviceType, Service $service): View
    {
        $this->authorizeWizardService($request, $service, $serviceType);

        $service->load(['translations.language.locale', 'city.state.country']);

        return $this->renderStepOne($request, $serviceType, $service);
    }

    /**
     * Shared step-1 view for create (service null) or edit.
     */
    private function renderStepOne(Request $request, ServiceType $serviceType, ?Service $service): View
    {
        $serviceType->load('translations.language.locale');

        $languages = $this->languagesForStepOneValidation();

        [$defaultCityId, $cityDisplayLabel] = $this->defaultCityForStepOne($request, $service);

        return view('services.wizard.step-1', [
            'serviceType' => $serviceType,
            'languages' => $languages,
            'service' => $service,
            'defaultCityId' => $defaultCityId,
            'cityDisplayLabel' => $cityDisplayLabel,
            'catalogHelperAccountTypeId' => \App\Support\CurrentCatalogHelperAccountContext::primaryAccountTypeId(),
        ]);
    }

    public function updateStepOne(Request $request, ServiceType $serviceType, Service $service): RedirectResponse
    {
        $this->authorizeWizardService($request, $service, $serviceType);

        $accountId = $this->assertProviderAccount($request);

        $languages = $this->languagesForStepOneValidation();

        $rules = [
            'city_id' => ['required', 'integer', 'exists:addons.lmp_cities,id'],
            'city_name' => ['required', 'string', 'max:255'],
            'translations' => ['required', 'array'],
        ];

        foreach ($languages as $language) {
            $rules["translations.{$language->id}.name"] = ['required', 'string', 'max:255'];
            $rules["translations.{$language->id}.description"] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules, [], $this->stepOneValidationAttributes($languages));

        $service->update([
            'city_id' => $validated['city_id'],
        ]);

        foreach ($languages as $language) {
            $translation = $validated['translations'][$language->id] ?? [];

            $service->translations()->updateOrCreate(
                ['language_id' => $language->id],
                [
                    'name' => $translation['name'] ?? null,
                    'description' => $translation['description'] ?? null,
                ]
            );
        }

        return redirect()
            ->route('services.wizard.step2', [
                'serviceType' => $serviceType->code,
                'service' => $service->id,
            ])
            ->with('status', __('wizard.step1_updated'));
    }

    private function authorizeWizardService(Request $request, Service $service, ServiceType $serviceType): void
    {
        $accountId = $this->assertProviderAccount($request);
        abort_unless((int) $service->account_id === (int) $accountId, 403);
        abort_unless((int) $service->service_type_id === (int) $serviceType->id, 404);
    }

    public function storeStepOne(Request $request, ServiceType $serviceType): RedirectResponse
    {
        $accountId = $this->assertProviderAccount($request);

        $languages = $this->languagesForStepOneValidation();

        $rules = [
            'city_id' => ['required', 'integer', 'exists:addons.lmp_cities,id'],
            'city_name' => ['required', 'string', 'max:255'],
            'translations' => ['required', 'array'],
        ];

        foreach ($languages as $language) {
            $rules["translations.{$language->id}.name"] = ['required', 'string', 'max:255'];
            $rules["translations.{$language->id}.description"] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules, [], $this->stepOneValidationAttributes($languages));

        $service = Service::query()->create([
            'account_id' => $accountId,
            'service_type_id' => $serviceType->id,
            'city_id' => $validated['city_id'],
            // New services start paused until the wizard is completed.
            'status' => 'onhold',
        ]);

        foreach ($languages as $language) {
            $translation = $validated['translations'][$language->id] ?? [];

            $service->translations()->create([
                'language_id' => $language->id,
                'name' => $translation['name'] ?? null,
                'description' => $translation['description'] ?? null,
            ]);
        }

        return redirect()
            ->route('services.wizard.step2', [
                'serviceType' => $serviceType->code,
                'service' => $service->id,
            ])
            ->with('status', __('wizard.step1_completed'));
    }

    public function createStepTwo(Request $request, ServiceType $serviceType, Service $service): View
    {
        $this->authorizeWizardService($request, $service, $serviceType);

        $serviceType->load('translations.language.locale');
        $service->load('translations.language.locale');

        return view('services.wizard.step-2', [
            'serviceType' => $serviceType,
            'service' => $service,
        ]);
    }

    public function createStepThree(Request $request, ServiceType $serviceType, Service $service): View
    {
        $this->authorizeWizardService($request, $service, $serviceType);

        $serviceType->load('translations.language.locale');
        $service->load('translations.language.locale');

        return view('services.wizard.step-3', [
            'serviceType' => $serviceType,
            'service' => $service,
        ]);
    }

    public function createStepFour(Request $request, ServiceType $serviceType, Service $service): View|RedirectResponse
    {
        $this->authorizeWizardService($request, $service, $serviceType);

        if (ServiceWizardSkipsVariantsStep::isSkippedForServiceTypeCode($serviceType->code)) {
            return redirect()->route('services.wizard.step5', [
                'serviceType' => $serviceType->code,
                'service' => $service->id,
            ]);
        }

        $serviceType->load('translations.language.locale');
        $service->load('translations.language.locale');

        return view('services.wizard.step-4', [
            'serviceType' => $serviceType,
            'service' => $service,
            'catalogHelperAccountTypeId' => \App\Support\CurrentCatalogHelperAccountContext::primaryAccountTypeId(),
        ]);
    }

    public function createStepFive(Request $request, ServiceType $serviceType, Service $service): View
    {
        $this->authorizeWizardService($request, $service, $serviceType);

        $serviceType->load('translations.language.locale');
        $service->load(['translations.language.locale', 'media']);

        return view('services.wizard.step-5', [
            'serviceType' => $serviceType,
            'service' => $service,
        ]);
    }

    public function createStepSix(Request $request, ServiceType $serviceType, Service $service): View
    {
        $this->authorizeWizardService($request, $service, $serviceType);

        $serviceType->load('translations.language.locale');
        $service->load('translations.language.locale');

        return view('services.wizard.step-6', [
            'serviceType' => $serviceType,
            'service' => $service,
        ]);
    }

    /**
     * Wizard step 7 — catalogue experiences linked to the service (all service types).
     */
    public function createStepSeven(Request $request, ServiceType $serviceType, Service $service): View
    {
        $this->authorizeWizardService($request, $service, $serviceType);

        $serviceType->load('translations.language.locale');
        $service->load('translations.language.locale');

        return view('services.wizard.step-7', [
            'serviceType' => $serviceType,
            'service' => $service,
        ]);
    }

    /**
     * Wizard step 8 — vertical-specific advanced options (gastronomy, hotel, activities, transfers, …).
     */
    public function createStepEight(Request $request, ServiceType $serviceType, Service $service): View
    {
        $this->authorizeWizardService($request, $service, $serviceType);
        abort_unless(ServiceWizardStepEight::isEnabledForServiceTypeCode($serviceType->code), 404);

        $serviceType->load('translations.language.locale');
        $service->load('translations.language.locale');

        return view('services.wizard.step-8', [
            'serviceType' => $serviceType,
            'service' => $service,
        ]);
    }

    public function searchCities(Request $request): JsonResponse
    {
        $this->assertProviderAccount($request);

        $query = trim((string) $request->query('q', ''));

        if (mb_strlen($query) < self::MIN_CITY_SEARCH_LENGTH) {
            return response()->json([]);
        }

        $cities = LmpCity::query()
            ->with(['state.country'])
            ->where('name', 'like', '%' . $query . '%')
            ->orderBy('name')
            ->limit(self::MAX_CITY_SEARCH_RESULTS + 1)
            ->get(['id', 'name', 'state_id']);

        $truncated = $cities->count() > self::MAX_CITY_SEARCH_RESULTS;
        if ($truncated) {
            $cities = $cities->take(self::MAX_CITY_SEARCH_RESULTS);
        }

        $results = $cities->map(fn (LmpCity $city) => [
            'id' => $city->id,
            'name' => $city->name,
            'label' => $this->formatCitySearchLabel($city),
        ])->values();

        return response()->json([
            'results' => $results,
            'truncated' => $truncated,
        ]);
    }

    /**
     * Languages ordered like the step-1 form (for validation rules and attribute names).
     *
     * @return Collection<int, Language>
     */
    private function languagesForStepOneValidation(): Collection
    {
        return Language::query()
            ->with('locale')
            ->get()
            ->values();
    }

    /**
     * Human-readable :attribute names for step-1 validation messages.
     *
     * @param  Collection<int, Language>  $languages
     * @return array<string, string>
     */
    private function stepOneValidationAttributes(Collection $languages): array
    {
        $attrs = [
            'city_id' => __('wizard.validation.city_id'),
            'city_name' => __('wizard.validation.city_name'),
            'translations' => __('wizard.validation.translations'),
        ];

        foreach ($languages as $language) {
            $id = (int) $language->id;
            $localeLabel = $language->display_name;
            $attrs["translations.{$id}.name"] = __('wizard.validation.translation_name', ['locale' => $localeLabel]);
            $attrs["translations.{$id}.description"] = __('wizard.validation.translation_description', ['locale' => $localeLabel]);
        }

        return $attrs;
    }

    /**
     * Default city for step 1: service city when editing, account address city when creating.
     *
     * @return array{0: int|null, 1: string}
     */
    private function defaultCityForStepOne(Request $request, ?Service $service): array
    {
        $cityId = $service?->city_id;

        if ($cityId === null && $service === null) {
            $account = $request->user()?->currentAccount();
            if ($account instanceof Account && $account->city_id !== null) {
                $cityId = (int) $account->city_id;
            }
        }

        if ($cityId === null || $cityId < 1) {
            return [null, ''];
        }

        $city = LmpCity::query()->with(['state.country'])->find($cityId);
        if ($city === null) {
            return [null, ''];
        }

        return [(int) $city->id, $this->formatCitySearchLabel($city)];
    }

    /**
     * City name plus state/province and country so duplicate city names are distinguishable in search.
     */
    private function formatCitySearchLabel(LmpCity $city): string
    {
        $stateName = $city->state?->name;
        $countryName = $city->state?->country?->name;
        $tail = array_filter([$stateName, $countryName], fn ($v) => $v !== null && $v !== '');

        if ($tail === []) {
            return $city->name;
        }

        return $city->name.' — '.implode(', ', $tail);
    }

    public function translateDescriptions(Request $request, TranslationService $translationService): JsonResponse
    {
        $this->assertProviderAccount($request);

        $validated = $request->validate(
            [
                'source_language_id' => ['required', 'integer', Rule::exists(Language::class, 'id')],
                'translations' => ['required', 'array'],
                'translations.*.name' => ['nullable', 'string'],
                'translations.*.description' => ['nullable', 'string'],
            ],
            [],
            [
                'source_language_id' => __('wizard.validation.source_language_id'),
                'translations' => __('wizard.validation.translations'),
                'translations.*.name' => __('wizard.validation.translation_field_name'),
                'translations.*.description' => __('wizard.validation.translation_field_description'),
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

    private function assertProviderAccount(Request $request): int
    {
        AccountBusinessTypeGate::assertProviderAccount($request);

        $accountId = $request->user()?->currentAccountId();
        abort_unless($accountId, 403);

        return (int) $accountId;
    }
}

