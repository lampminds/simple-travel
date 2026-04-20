<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\TodoCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AccountTasksController extends Controller
{
    /**
     * Read-only list of onboarding tasks for the current account, grouped by category.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        abort_unless($user->hasRoleForCurrentAccount('owner'), 403);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $taskScope = function ($query) use ($account): void {
            $query->where('account_id', $account->id)
                ->where('active', true);
        };

        $categories = TodoCategory::query()
            ->where('active', true)
            ->whereHas('tasks', $taskScope)
            ->with([
                'translations.language.locale',
                'tasks' => function ($query) use ($taskScope): void {
                    $taskScope($query);
                    $query->with(['translations.language.locale'])->orderBy('sort_order');
                },
            ])
            ->orderBy('sort_order')
            ->get();

        return view('account.tasks', [
            'account' => $account,
            'categories' => $categories,
        ]);
    }
}
