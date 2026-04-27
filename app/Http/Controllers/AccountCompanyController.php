<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\LmpCity;
use App\Models\TodoTask;
use App\Models\TodoTaskUserAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class AccountCompanyController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        abort_unless($user->hasRoleForCurrentAccount('owner'), 403);

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
        ]);
    }

    public function cityDetails(Request $request, int $cityId): JsonResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($user->hasRoleForCurrentAccount('owner'), 403);

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

        abort_unless($user->hasRoleForCurrentAccount('owner'), 403);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'commercial_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            // Use the model class so validation runs on connection `addons` (see LmpCity::$connection).
            'city_id' => ['required', 'integer', Rule::exists(LmpCity::class, 'id')],
            'postal_code' => ['required', 'string', 'max:255'],
        ]);

        $account->fill($data);
        $account->save();
        $this->registerCompleteProfileTaskCompletion($account->id, $user->id);

        return redirect()
            ->route('account.dashboard')
            ->with('status', 'Los datos de la empresa se han actualizado.');
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

