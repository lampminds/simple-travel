<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\LmpCity;
use App\Models\Service;
use App\Models\ServiceType;
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

        $languages = Language::query()
            ->with('locale')
            ->get()
            ->sortBy(fn (Language $language) => $language->display_name)
            ->values();

        $cityDisplayLabel = '';
        if ($service?->city_id) {
            $city = LmpCity::query()->with(['state.country'])->find($service->city_id);
            if ($city) {
                $cityDisplayLabel = $this->formatCitySearchLabel($city);
            }
        }

        return view('services.wizard.step-1', [
            'serviceType' => $serviceType,
            'languages' => $languages,
            'service' => $service,
            'cityDisplayLabel' => $cityDisplayLabel,
        ]);
    }

    public function updateStepOne(Request $request, ServiceType $serviceType, Service $service): RedirectResponse
    {
        $this->authorizeWizardService($request, $service, $serviceType);

        $accountId = $request->user()?->currentAccountId();
        abort_unless($accountId, 403);

        $languages = Language::query()->get(['id']);

        $rules = [
            'city_id' => ['required', 'integer', 'exists:addons.lmp_cities,id'],
            'city_name' => ['required', 'string', 'max:255'],
            'translations' => ['required', 'array'],
        ];

        foreach ($languages as $language) {
            $rules["translations.{$language->id}.name"] = ['required', 'string', 'max:255'];
            $rules["translations.{$language->id}.description"] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules);

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
        $accountId = $request->user()?->currentAccountId();
        abort_unless($accountId, 403);
        abort_unless((int) $service->account_id === (int) $accountId, 403);
        abort_unless((int) $service->service_type_id === (int) $serviceType->id, 404);
    }

    public function storeStepOne(Request $request, ServiceType $serviceType): RedirectResponse
    {
        $accountId = $request->user()?->currentAccountId();
        abort_unless($accountId, 403);

        $languages = Language::query()->get(['id']);

        $rules = [
            'city_id' => ['required', 'integer', 'exists:addons.lmp_cities,id'],
            'city_name' => ['required', 'string', 'max:255'],
            'translations' => ['required', 'array'],
        ];

        foreach ($languages as $language) {
            $rules["translations.{$language->id}.name"] = ['required', 'string', 'max:255'];
            $rules["translations.{$language->id}.description"] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules);

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

    public function createStepFour(Request $request, ServiceType $serviceType, Service $service): View
    {
        $this->authorizeWizardService($request, $service, $serviceType);

        $serviceType->load('translations.language.locale');
        $service->load('translations.language.locale');

        return view('services.wizard.step-4', [
            'serviceType' => $serviceType,
            'service' => $service,
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

    public function searchCities(Request $request): JsonResponse
    {
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
        $validated = $request->validate([
            'source_language_id' => ['required', 'integer', Rule::exists(Language::class, 'id')],
            'translations' => ['required', 'array'],
            'translations.*.name' => ['nullable', 'string'],
            'translations.*.description' => ['nullable', 'string'],
        ]);

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
}

