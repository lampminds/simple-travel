<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthLoginRedirectService;
use App\Models\UserInvitation;
use App\Services\UserInvitationAcceptanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request, UserInvitationAcceptanceService $acceptanceService)
    {
        $request->authenticate();
        $request->session()->regenerate();

        $redirect = app(AuthLoginRedirectService::class)->handle($request, $request->user());

        $token = trim((string) $request->input('invitation_token', ''));
        if ($token !== '') {
            $invitation = UserInvitation::query()->where('token', $token)->first();
            if ($invitation && $acceptanceService->acceptInternalForExistingUser($invitation, $request->user())) {
                return redirect()
                    ->route('account.dashboard')
                    ->with('status', __('invitations.internal_accepted_existing'));
            }
            if ($invitation && $acceptanceService->acceptExternalForExistingUser($invitation, $request->user())) {
                return redirect()
                    ->route('account.relationships.index')
                    ->with('status', __('invitations.accepted_relationship'));
            }
        }

        return $redirect;
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
