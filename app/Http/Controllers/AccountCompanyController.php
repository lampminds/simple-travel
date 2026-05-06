<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\AccountTaxId;
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
    public function edit(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);
        $account->loadMissing('typeCategories.translations.language');

        $accountTypeLabel = $account->typeCategories
            ->pluck('name')
            ->filter()
            ->implode(' / ');

        $cityId = old('city_id', $account->city_id);
        $currentCity = null;
        if (is_numeric($cityId)) {
            $currentCity = LmpCity::query()
                ->with(['state.country'])
                ->find((int) $cityId);
        }

        return view('account.company-edit', [
            'account' => $account,
            'currentCity' => $currentCity,
            'accountTypeLabel' => $accountTypeLabel !== '' ? $accountTypeLabel : '—',
            'taxIdCategories' => AccountCategory::query()
                ->byGroup('tax_id')
                ->where('active', true)
                ->with(['translations.language'])
                ->ordered()
                ->get(),
            'taxIds' => $account->taxIds()
                ->with(['category.translations.language'])
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
                'email' => ['required', 'email', 'max:255'],
                'phone' => ['required', 'string', 'max:255'],
                'address_line1' => ['required', 'string', 'max:255'],
                'address_line2' => ['nullable', 'string', 'max:255'],
                // Use the model class so validation runs on connection `addons` (see LmpCity::$connection).
                'city_id' => ['required', 'integer', Rule::exists(LmpCity::class, 'id')],
                'postal_code' => ['required', 'string', 'max:255'],
                'tax_ids' => ['array'],
                'tax_ids.*.id' => ['nullable', 'integer', 'exists:account_tax_ids,id'],
                'tax_ids.*.account_category_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('cat_account_categories', 'id')->where('group', 'tax_id'),
                    'required_with:tax_ids.*.value',
                ],
                'tax_ids.*.value' => ['nullable', 'string', 'max:255', 'required_with:tax_ids.*.account_category_id'],
                'tax_ids.*.delete' => ['nullable', 'boolean'],
            ],
            [
                'tax_ids.*.account_category_id.required_with' => 'Seleccioná un tipo fiscal.',
                'tax_ids.*.value.required_with' => 'Ingresá el valor fiscal.',
            ]
        );

        $account->fill(Arr::only($data, [
            'name',
            'commercial_name',
            'email',
            'phone',
            'address_line1',
            'address_line2',
            'city_id',
            'postal_code',
        ]));
        $account->save();
        $this->assertNoDuplicateTaxIdTypes($data['tax_ids'] ?? []);
        $this->syncTaxIds($account, $data['tax_ids'] ?? []);
        $this->registerCompleteProfileTaskCompletion($account->id, $user->id);

        return redirect()
            ->route('account.dashboard')
            ->with('status', 'Los datos de la empresa se han actualizado.');
    }

    /**
     * Sync account tax IDs from form rows (create, update, delete).
     *
     * @param  array<int, array{id?: int|null, account_category_id?: int|null, value?: string|null, delete?: bool|null}>  $rows
     */
    private function syncTaxIds(Account $account, array $rows): void
    {
        foreach ($rows as $row) {
            $id = isset($row['id']) ? (int) $row['id'] : null;
            $delete = (bool) ($row['delete'] ?? false);
            $categoryId = isset($row['account_category_id']) ? (int) $row['account_category_id'] : 0;
            $value = trim((string) ($row['value'] ?? ''));

            if ($id !== null) {
                $existing = $account->taxIds()->whereKey($id)->first();
                if (! $existing instanceof AccountTaxId) {
                    continue;
                }
                if ($delete) {
                    $existing->delete();
                    continue;
                }
                if ($categoryId < 1 || $value === '') {
                    continue;
                }
                $existing->update([
                    'account_category_id' => $categoryId,
                    'value' => $value,
                ]);
                continue;
            }

            if ($delete || $categoryId < 1 || $value === '') {
                continue;
            }

            $account->taxIds()->create([
                'account_category_id' => $categoryId,
                'value' => $value,
            ]);
        }
    }

    /**
     * Prevent duplicate tax-id category rows in the same submit.
     *
     * @param  array<int, array{account_category_id?: int|null, delete?: bool|null}>  $rows
     */
    private function assertNoDuplicateTaxIdTypes(array $rows): void
    {
        $firstIndexByCategory = [];

        foreach ($rows as $idx => $row) {
            if ((bool) ($row['delete'] ?? false)) {
                continue;
            }

            $categoryId = isset($row['account_category_id']) ? (int) $row['account_category_id'] : 0;
            if ($categoryId < 1) {
                continue;
            }

            if (! isset($firstIndexByCategory[$categoryId])) {
                $firstIndexByCategory[$categoryId] = $idx;
                continue;
            }

            throw ValidationException::withMessages([
                "tax_ids.$idx.account_category_id" => 'No podés repetir el mismo tipo fiscal.',
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
}

