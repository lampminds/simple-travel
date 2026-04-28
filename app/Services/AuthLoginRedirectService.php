<?php

namespace App\Services;

use App\Http\Middleware\RecordLastLogin;
use App\Http\Middleware\SetPermissionsTeamForRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Support\CurrentAccountSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuthLoginRedirectService
{
    public function handle(Request $request, User $user): RedirectResponse
    {
        if (! $request->session()->has('locale')) {
            $request->session()->put('locale', config('app.locale'));
        }

        $request->session()->put(RecordLastLogin::SESSION_KEY, true);

        $accountIds = $user->accounts()
            ->orderBy('accounts.id')
            ->pluck('accounts.id')
            ->map(fn ($id) => (int) $id);

        if ($accountIds->count() === 1) {
            CurrentAccountSession::put($request, $user, (int) $accountIds->first());
        } elseif ($accountIds->count() > 1) {
            $request->session()->forget(SetPermissionsTeamForRequest::SESSION_CURRENT_ACCOUNT_ID);
            $request->session()->forget(CurrentAccountSession::SESSION_ACCOUNT_NAME);
            $request->session()->forget(CurrentAccountSession::SESSION_ACCOUNT_TYPE_IDS);
            $request->session()->put(SetPermissionsTeamForRequest::SESSION_REQUIRES_ACCOUNT_SELECTION, true);

            return redirect()->route('account.select');
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
