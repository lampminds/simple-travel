<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetPermissionsTeamForRequest;
use App\Support\CurrentAccountSession;
use App\Support\OperatorWebsiteAgencyRedirect;
use App\Support\SafeReturnUrl;
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

            if (OperatorWebsiteAgencyRedirect::applies($request, $user->currentAccount())) {
                return OperatorWebsiteAgencyRedirect::toLanding($request);
            }

            return redirect()->intended(route('account.dashboard'));
        }

        return view('account.select-account', [
            'accounts' => $accounts,
            'returnPath' => SafeReturnUrl::relativePath(
                $request,
                route('account.dashboard', absolute: false),
            ),
        ]);
    }
}

