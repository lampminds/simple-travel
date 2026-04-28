<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSocialAccount;
use App\Services\AuthLoginRedirectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    /**
     * @var array<int, string>
     */
    private const ALLOWED_PROVIDERS = ['google', 'facebook'];

    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::ALLOWED_PROVIDERS, true), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::ALLOWED_PROVIDERS, true), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable) {
            return redirect()->route('login')->with('error', __('auth.login.social_auth_failed'));
        }

        $providerUserId = trim((string) $socialUser->getId());
        if ($providerUserId === '') {
            return redirect()->route('login')->with('error', __('auth.login.social_auth_failed'));
        }

        $socialAccount = UserSocialAccount::query()
            ->where('provider', $provider)
            ->where('provider_user_id', $providerUserId)
            ->first();

        if ($socialAccount) {
            $user = $socialAccount->user;
        } else {
            $email = mb_strtolower(trim((string) ($socialUser->getEmail() ?? '')));
            if ($email === '') {
                return redirect()->route('login')->with('error', __('auth.login.social_email_required'));
            }

            $user = User::query()->where('email', $email)->first();
            if (! $user) {
                return redirect()->route('login')->with('error', __('auth.login.social_user_not_found'));
            }

            UserSocialAccount::query()->create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_user_id' => $providerUserId,
                'provider_email' => $email,
                'avatar_url' => $socialUser->getAvatar(),
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return app(AuthLoginRedirectService::class)->handle($request, $user);
    }
}
