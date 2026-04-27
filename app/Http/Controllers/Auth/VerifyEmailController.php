<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AccountStartupService;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    private const SESSION_STARTUP_ACCOUNT_ID_AFTER_VERIFY = 'startup_account_id_after_verify';

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     */
    public function __invoke(EmailVerificationRequest $request, AccountStartupService $accountStartupService): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME)
                ->with('status', __('auth.verification.already_verified'));
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        $startupAccountId = (int) $request->session()->pull(self::SESSION_STARTUP_ACCOUNT_ID_AFTER_VERIFY, 0);
        if ($startupAccountId > 0 && $request->user()->belongsToAccount($startupAccountId)) {
            $accountStartupService->runForNewAccount(
                $startupAccountId,
                (int) $request->user()->id
            );
        }

        if ($request->session()->pull('welcome_company_after_verify')) {
            return redirect()
                ->route('welcome.company')
                ->with('status', __('auth.verification.verified'));
        }

        return redirect()->intended(RouteServiceProvider::HOME)
            ->with('status', __('auth.verification.verified'));
    }
}
