<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\TodoTask;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class WelcomeCompanyController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $todoTasks = TodoTask::query()
            ->where('account_id', (int) $account->getKey())
            ->where('active', true)
            ->whereHas(
                'category',
                fn ($query) => $query->where('code', 'account_setup')
            )
            ->with(['translations.language.locale'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('pages.welcome-company', [
            'todoTasks' => $todoTasks,
        ]);
    }
}
