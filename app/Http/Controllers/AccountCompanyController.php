<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountContactMethod;
use App\Models\AccountDocument;
use App\Models\AccountType;
use App\Models\ContactType;
use App\Models\CatDocument;
use App\Models\LmpCity;
use App\Models\TodoTask;
use App\Models\TodoTaskUserAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class AccountCompanyController extends Controller
{
    private const MIN_CITY_SEARCH_LENGTH = 4;

    private const MAX_CITY_SEARCH_RESULTS = 2000;

    public function edit(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);
        $account->loadMissing('accountTypes.translations.language');

        $companyTypes = AccountType::query()
            ->with('translations')
            ->where('active', true)
            ->ordered()
            ->get()
            ->mapWithKeys(fn ($cat) => [
                $cat->id => ['name' => $cat->name, 'description' => $cat->description ?? ''],
            ]);

        $selectedCompanyTypeIds = $account->accountTypes
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $primaryAddress = $account->primaryAddress();
        $cityId = old('city_id', $primaryAddress?->city_id);
        $currentCity = null;
        if (is_numeric($cityId)) {
            $currentCity = LmpCity::query()
                ->with(['state.country'])
                ->find((int) $cityId);
        }

        return view('account.company-edit', [
            'account' => $account,
            'primaryAddress' => $primaryAddress,
            'currentCity' => $currentCity,
            'companyTypes' => $companyTypes,
            'selectedCompanyTypeIds' => $selectedCompanyTypeIds,
            'taxIdCategories' => CatDocument::query()
                ->byGroup('tax_id')
                ->where('active', true)
                ->with(['translations.language'])
                ->ordered()
                ->get(),
            'taxIds' => $account->documents()
                ->with(['document.translations.language'])
                ->orderBy('id')
                ->get(),
            'contactTypes' => ContactType::query()
                ->where('active', true)
                ->with(['translations.language.locale'])
                ->orderBy('sort_order')
                ->get(),
            'contactMethods' => $account->contactMethods()
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function cityDetails(Request $request, int $cityId): JsonResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        $city = LmpCity::query()
            ->with(['state.country'])
            ->find($cityId);

        if (! $city) {
            return response()->json(['message' => 'City not found.'], 404);
        }

        return response()->json([
            'id' => $city->id,
            'name' => $city->name,
            'state' => $city->state?->name,
            'country' => $city->state?->country?->name,
        ]);
    }

    public function searchCities(Request $request): JsonResponse
    {
        abort_unless($request->user() !== null, 401);

        $query = trim((string) $request->query('q', ''));

        if (mb_strlen($query) < self::MIN_CITY_SEARCH_LENGTH) {
            return response()->json([]);
        }

        $cities = LmpCity::query()
            ->with(['state.country'])
            ->where('name', 'like', '%'.$query.'%')
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

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $data = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'commercial_name' => ['required', 'string', 'max:255'],
                'address_line1' => ['required', 'string', 'max:255'],
                'address_line2' => ['nullable', 'string', 'max:255'],
                // Use the model class so validation runs on connection `addons` (see LmpCity::$connection).
                'city_id' => ['required', 'integer', Rule::exists(LmpCity::class, 'id')],
                'postal_code' => ['required', 'string', 'max:255'],
                'tax_ids' => ['array'],
                'tax_ids.*.id' => ['nullable', 'integer', 'exists:account_documents,id'],
                'tax_ids.*.document_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('cat_documents', 'id')->where('group', 'tax_id'),
                    'required_with:tax_ids.*.value',
                ],
                'tax_ids.*.value' => ['nullable', 'string', 'max:255', 'required_with:tax_ids.*.document_id'],
                'tax_ids.*.delete' => ['nullable', 'boolean'],
                'contact_methods' => ['array'],
                'contact_methods.*.id' => ['nullable', 'integer', 'exists:account_contact_methods,id'],
                'contact_methods.*.contact_type_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('cat_contact_types', 'id')->where('active', true),
                    'required_with:contact_methods.*.value',
                ],
                'contact_methods.*.value' => ['nullable', 'string', 'max:255', 'required_with:contact_methods.*.contact_type_id'],
                'contact_methods.*.is_primary' => ['nullable', 'boolean'],
                'contact_methods.*.delete' => ['nullable', 'boolean'],
            ],
            [
                'tax_ids.*.document_id.required_with' => 'Selecciona un tipo fiscal.',
                'tax_ids.*.value.required_with' => 'Ingresa el valor fiscal.',
                'contact_methods.*.contact_type_id.required_with' => 'Selecciona un tipo de contacto.',
                'contact_methods.*.value.required_with' => 'Ingresa el medio de contacto.',
            ]
        );

        $account->fill(Arr::only($data, [
            'name',
            'commercial_name',
        ]));
        $account->save();
        $account->syncPrimaryAddress($data);
        $this->assertNoDuplicateTaxIdTypes($data['tax_ids'] ?? []);
        $this->assertContactMethods($data['contact_methods'] ?? []);
        $this->syncTaxIds($account, $data['tax_ids'] ?? []);
        $this->syncContactMethods($account, $data['contact_methods'] ?? []);
        $this->registerCompleteProfileTaskCompletion($account->id, $user->id);

        return redirect()
            ->route('account.dashboard')
            ->with('status', 'Los datos de la empresa se han actualizado.');
    }

    /**
     * Sync account tax IDs from form rows (create, update, delete).
     *
     * @param  array<int, array{id?: int|null, document_id?: int|null, value?: string|null, delete?: bool|null}>  $rows
     */
    private function syncTaxIds(Account $account, array $rows): void
    {
        foreach ($rows as $row) {
            $id = isset($row['id']) ? (int) $row['id'] : null;
            $delete = (bool) ($row['delete'] ?? false);
            $documentId = isset($row['document_id']) ? (int) $row['document_id'] : 0;
            $value = trim((string) ($row['value'] ?? ''));

            if ($id !== null) {
                $existing = $account->documents()->whereKey($id)->first();
                if (! $existing instanceof AccountDocument) {
                    continue;
                }
                if ($delete) {
                    $existing->delete();
                    continue;
                }
                if ($documentId < 1 || $value === '') {
                    continue;
                }
                $existing->update([
                    'document_id' => $documentId,
                    'value' => $value,
                ]);
                continue;
            }

            if ($delete || $documentId < 1 || $value === '') {
                continue;
            }

            $account->documents()->create([
                'document_id' => $documentId,
                'value' => $value,
            ]);
        }
    }

    /**
     * Sync account contact methods from form rows (create, update, delete).
     *
     * @param  array<int, array{id?: int|null, contact_type_id?: int|null, value?: string|null, is_primary?: bool|null, delete?: bool|null}>  $rows
     */
    private function syncContactMethods(Account $account, array $rows): void
    {
        foreach ($rows as $row) {
            $id = isset($row['id']) ? (int) $row['id'] : null;
            $delete = (bool) ($row['delete'] ?? false);
            $typeId = isset($row['contact_type_id']) ? (int) $row['contact_type_id'] : 0;
            $value = trim((string) ($row['value'] ?? ''));
            $isPrimary = (bool) ($row['is_primary'] ?? false);

            if ($id !== null) {
                $existing = $account->contactMethods()->whereKey($id)->first();
                if (! $existing instanceof AccountContactMethod) {
                    continue;
                }
                if ($delete) {
                    $existing->delete();
                    continue;
                }
                if ($typeId < 1 || $value === '') {
                    continue;
                }
                $existing->update([
                    'contact_type_id' => $typeId,
                    'value' => $value,
                    'is_primary' => $isPrimary,
                ]);
                continue;
            }

            if ($delete || $typeId < 1 || $value === '') {
                continue;
            }

            $account->contactMethods()->create([
                'contact_type_id' => $typeId,
                'value' => $value,
                'is_primary' => $isPrimary,
                'is_verified' => false,
            ]);
        }
    }

    /**
     * Reject duplicate channel values and invalid emails in the same submit.
     *
     * @param  array<int, array{contact_type_id?: int|null, value?: string|null, delete?: bool|null}>  $rows
     */
    private function assertContactMethods(array $rows): void
    {
        $typeIds = [];
        foreach ($rows as $row) {
            if ((bool) ($row['delete'] ?? false)) {
                continue;
            }
            $typeId = isset($row['contact_type_id']) ? (int) $row['contact_type_id'] : 0;
            if ($typeId > 0) {
                $typeIds[] = $typeId;
            }
        }

        $emailTypeIds = ContactType::query()
            ->whereIn('id', array_unique($typeIds))
            ->get()
            ->filter(fn (ContactType $type): bool => ($type->getRawOriginal('code') ?? '') === 'email')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $seen = [];
        foreach ($rows as $idx => $row) {
            if ((bool) ($row['delete'] ?? false)) {
                continue;
            }

            $typeId = isset($row['contact_type_id']) ? (int) $row['contact_type_id'] : 0;
            $value = trim((string) ($row['value'] ?? ''));
            if ($typeId < 1 || $value === '') {
                continue;
            }

            if (in_array($typeId, $emailTypeIds, true) && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                throw ValidationException::withMessages([
                    "contact_methods.$idx.value" => 'Ingresa un correo electrónico válido.',
                ]);
            }

            $key = $typeId.'|'.mb_strtolower($value);
            if (isset($seen[$key])) {
                throw ValidationException::withMessages([
                    "contact_methods.$idx.value" => 'Ese medio de contacto ya está cargado.',
                ]);
            }
            $seen[$key] = true;
        }
    }

    /**
     * Prevent duplicate tax-id category rows in the same submit.
     *
     * @param  array<int, array{document_id?: int|null, delete?: bool|null}>  $rows
     */
    private function assertNoDuplicateTaxIdTypes(array $rows): void
    {
        $firstIndexByDocument = [];

        foreach ($rows as $idx => $row) {
            if ((bool) ($row['delete'] ?? false)) {
                continue;
            }

            $documentId = isset($row['document_id']) ? (int) $row['document_id'] : 0;
            if ($documentId < 1) {
                continue;
            }

            if (! isset($firstIndexByDocument[$documentId])) {
                $firstIndexByDocument[$documentId] = $idx;
                continue;
            }

            throw ValidationException::withMessages([
                "tax_ids.$idx.document_id" => 'No puedes repetir el mismo tipo fiscal.',
            ]);
        }
    }

    /**
     * Mark the "complete_profile" onboarding task as completed for current user
     * only when nobody in the same account has completed it yet.
     */
    private function registerCompleteProfileTaskCompletion(int $accountId, int $userId): void
    {
        $task = TodoTask::query()
            ->where('account_id', $accountId)
            ->where('code', 'complete_profile')
            ->first();

        if (! $task) {
            return;
        }

        $alreadyCompletedByAccount = $task->userAssignments()
            ->where('status', 'completed')
            ->exists();

        if ($alreadyCompletedByAccount) {
            return;
        }

        TodoTaskUserAssignment::query()->updateOrCreate(
            [
                'todo_task_id' => $task->id,
                'user_id' => $userId,
            ],
            [
                'status' => 'completed',
                'completed_at' => now(),
                'ignored_at' => null,
            ]
        );
    }

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
}
