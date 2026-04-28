<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\WebsiteImpersonationTokenService;
use App\Support\CurrentAccountSession;
use App\Support\WebsiteImpersonationSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Support access via one-time link (other browser). No admin session required to redeem.
 */
final class WebsiteImpersonationController extends Controller
{
    public function enter(Request $request, string $token, WebsiteImpersonationTokenService $tokenService): RedirectResponse
    {
        $payload = $tokenService->pullPayload($token);
        if ($payload === null) {
            return redirect()
                ->route('login')
                ->with('error', __('impersonation.token_invalid_or_used'));
        }

        $user = User::query()->find($payload['user_id']);
        if (! $user instanceof User) {
            return redirect()
                ->route('login')
                ->with('error', __('impersonation.token_invalid_or_used'));
        }

        if ($user->belongsToPlatformAccount()) {
            return redirect()
                ->route('login')
                ->with('error', __('impersonation.token_invalid_or_used'));
        }

        $platformAccountId = (int) config('permission.platform_account_id', 1);
        $contextAccountId = $this->resolveContextAccountIdForUser($user, $platformAccountId);
        if ($contextAccountId === null) {
            return redirect()
                ->route('login')
                ->with('error', __('impersonation.target_no_account'));
        }

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put(WebsiteImpersonationSession::KEY_ACTIVE_VIA_SUPPORT_TOKEN, true);
        CurrentAccountSession::put($request, $user, $contextAccountId);

        return redirect()->route('account.dashboard');
    }

    /**
     * Active account in session: prefer a non-platform tenant account, else any linked account.
     */
    private function resolveContextAccountIdForUser(User $user, int $platformAccountId): ?int
    {
        $tenantId = $user->accounts()
            ->where('accounts.id', '!=', $platformAccountId)
            ->orderBy('accounts.id')
            ->value('accounts.id');

        if (is_int($tenantId) && $tenantId > 0) {
            return $tenantId;
        }

        $anyId = $user->accounts()->orderBy('accounts.id')->value('accounts.id');

        return is_int($anyId) && $anyId > 0 ? $anyId : null;
    }
}
