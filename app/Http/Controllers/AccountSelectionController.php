<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetPermissionsTeamForRequest;
use App\Support\CurrentAccountSession;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountSelectionController extends Controller
{
    /**
     * Ask the user which account to operate after login when multiple memberships exist.
     */
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if ($user === null) {
            return redirect()->route('login');
        }

        $accounts = $user->switchableAccounts();
        if ($accounts->count() <= 1) {
            $accountId = $accounts->first()?->id;
            if ($accountId !== null) {
                CurrentAccountSession::put($request, $user, (int) $accountId);
            }
            $request->session()->forget(SetPermissionsTeamForRequest::SESSION_REQUIRES_ACCOUNT_SELECTION);

            return redirect()->route('account.dashboard');
        }

        return view('account.select-account', [
            'accounts' => $accounts,
        ]);
    }
}

