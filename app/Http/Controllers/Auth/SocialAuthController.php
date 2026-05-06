<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInvitation;
use App\Models\UserSocialAccount;
use App\Services\AuthLoginRedirectService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

class SocialAuthController extends Controller
{
    /**
     * @var array<int, string>
     */
    private const ALLOWED_PROVIDERS = ['google', 'facebook'];
    private const SESSION_INVITATION_TOKEN = 'social_invitation_token';

    public function redirect(Request $request, string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::ALLOWED_PROVIDERS, true), 404);

        $invitation = $this->resolveInternalInvitation($request->string('invitation_token')->trim()->value());
        if (! $invitation) {
            return redirect()->route('login')->with('error', __('auth.login.social_invitation_only'));
        }

        $request->session()->put(self::SESSION_INVITATION_TOKEN, $invitation->token);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::ALLOWED_PROVIDERS, true), 404);

        $invitationToken = (string) $request->session()->pull(self::SESSION_INVITATION_TOKEN, '');
        $invitation = $this->resolveInternalInvitation($invitationToken);
        if (! $invitation) {
            return redirect()->route('login')->with('error', __('auth.login.social_invitation_only'));
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable) {
            return redirect()->route('register', ['token' => $invitation->token])->with('error', __('auth.register.social_auth_failed'));
        }

        $providerUserId = trim((string) $socialUser->getId());
        if ($providerUserId === '') {
            return redirect()->route('register', ['token' => $invitation->token])->with('error', __('auth.register.social_auth_failed'));
        }

        $socialAccount = UserSocialAccount::query()
            ->where('provider', $provider)
            ->where('provider_user_id', $providerUserId)
            ->first();

        $user = null;

        if ($socialAccount) {
            $user = $socialAccount->user;
            $emailFromSocial = mb_strtolower(trim((string) ($socialUser->getEmail() ?? '')));
            if ($emailFromSocial !== '' && $emailFromSocial !== mb_strtolower($invitation->email)) {
                return redirect()->route('register', ['token' => $invitation->token])->with('error', __('auth.register.invitation_email_mismatch'));
            }

            $pendingMatch =
                $user->activation_state === User::ACTIVATION_PENDING_INVITATION
                && $invitation->invited_user_id !== null
                && (int) $invitation->invited_user_id === (int) $user->getKey()
                && $invitation->isUsable();

            if ($pendingMatch) {
                DB::transaction(function () use ($user, $invitation, $socialUser, $emailFromSocial) {
                    $email = $emailFromSocial !== '' ? $emailFromSocial : mb_strtolower($invitation->email);
                    $name = trim((string) ($socialUser->getName() ?? ''));
                    if ($name === '') {
                        $name = trim((string) ($invitation->name ?? '')) ?: Str::before($email, '@');
                    }
                    $titled = Str::title($name);

                    $user->forceFill([
                        'name' => $titled,
                        'activation_state' => User::ACTIVATION_ACTIVE,
                        'password' => Hash::make(Str::random(40)),
                    ])->save();

                    $person = $user->persons()->orderBy('persons.id')->first();
                    if ($person !== null) {
                        $person->forceFill(['name' => $titled])->save();
                    }

                    $invitation->forceFill([
                        'email' => $email,
                        'name' => $titled,
                        'status' => UserInvitation::STATUS_ACCEPTED,
                        'accepted_at' => now(),
                    ])->save();
                });

                event(new Registered($user));
            } elseif ($user->activation_state === User::ACTIVATION_PENDING_INVITATION) {
                return redirect()
                    ->route('register', ['token' => $invitation->token])
                    ->with('error', __('invitations.invitation_invalid_state'));
            }
        } else {
            $email = mb_strtolower(trim((string) ($socialUser->getEmail() ?? '')));
            if ($email === '') {
                return redirect()->route('register', ['token' => $invitation->token])->with('error', __('auth.register.social_email_required'));
            }

            if ($email !== mb_strtolower($invitation->email)) {
                return redirect()->route('register', ['token' => $invitation->token])->with('error', __('auth.register.invitation_email_mismatch'));
            }

            $existing = User::query()->where('email', $email)->first();

            if ($existing !== null) {
                $stubMatch =
                    $existing->activation_state === User::ACTIVATION_PENDING_INVITATION
                    && $invitation->invited_user_id !== null
                    && (int) $invitation->invited_user_id === (int) $existing->getKey()
                    && $invitation->isUsable();

                if ($stubMatch) {
                    $user = DB::transaction(function () use ($socialUser, $email, $provider, $providerUserId, $invitation, $existing) {
                        $name = trim((string) ($socialUser->getName() ?? ''));
                        if ($name === '') {
                            $name = trim((string) ($invitation->name ?? '')) ?: Str::before($email, '@');
                        }
                        $titled = Str::title($name);

                        $existing->forceFill([
                            'name' => $titled,
                            'activation_state' => User::ACTIVATION_ACTIVE,
                            'password' => Hash::make(Str::random(40)),
                        ])->save();

                        $person = $existing->persons()->orderBy('persons.id')->first();
                        if ($person !== null) {
                            $person->forceFill(['name' => $titled])->save();
                        }

                        UserSocialAccount::query()->create([
                            'user_id' => $existing->id,
                            'provider' => $provider,
                            'provider_user_id' => $providerUserId,
                            'provider_email' => $email,
                            'avatar_url' => $socialUser->getAvatar(),
                        ]);

                        $invitation->forceFill([
                            'email' => $email,
                            'name' => $titled,
                            'status' => UserInvitation::STATUS_ACCEPTED,
                            'accepted_at' => now(),
                        ])->save();

                        return $existing;
                    });

                    event(new Registered($user));
                } elseif ($existing->activation_state === User::ACTIVATION_ACTIVE) {
                    return redirect()->route('login')->with('error', __('auth.login.social_existing_user_use_password'));
                } else {
                    return redirect()
                        ->route('register', ['token' => $invitation->token])
                        ->with('error', __('invitations.invitation_invalid_state'));
                }
            } elseif ($invitation->invited_user_id !== null) {
                return redirect()
                    ->route('register', ['token' => $invitation->token])
                    ->with('error', __('invitations.invitee_record_missing'));
            } else {
                $user = DB::transaction(function () use ($socialUser, $email, $provider, $providerUserId, $invitation) {
                    $name = trim((string) ($socialUser->getName() ?? ''));
                    if ($name === '') {
                        $name = trim((string) ($invitation->name ?? '')) ?: Str::before($email, '@');
                    }

                    $created = User::query()->create([
                        'name' => Str::title($name),
                        'email' => $email,
                        'password' => Hash::make(Str::random(40)),
                        'activation_state' => User::ACTIVATION_ACTIVE,
                    ]);

                    $created->accounts()->attach($invitation->account_id);

                    $role = Role::query()
                        ->where('account_id', $invitation->account_id)
                        ->whereKey($invitation->role_id)
                        ->firstOrFail();

                    app(PermissionRegistrar::class)->setPermissionsTeamId($invitation->account_id);
                    $created->assignRole($role);

                    UserSocialAccount::query()->create([
                        'user_id' => $created->id,
                        'provider' => $provider,
                        'provider_user_id' => $providerUserId,
                        'provider_email' => $email,
                        'avatar_url' => $socialUser->getAvatar(),
                    ]);

                    $invitation->forceFill([
                        'email' => $email,
                        'name' => Str::title($name),
                        'status' => UserInvitation::STATUS_ACCEPTED,
                        'accepted_at' => now(),
                    ])->save();

                    return $created;
                });

                event(new Registered($user));
            }
        }

        if ($user === null) {
            return redirect()->route('register', ['token' => $invitation->token])->with('error', __('auth.register.social_auth_failed'));
        }

        if (! $user->belongsToAccount((int) $invitation->account_id)) {
            return redirect()->route('login')->with('error', __('auth.login.social_invitation_only'));
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return app(AuthLoginRedirectService::class)->handle($request, $user);
    }

    private function resolveInternalInvitation(string $token): ?UserInvitation
    {
        if ($token === '') {
            return null;
        }

        $invitation = UserInvitation::query()
            ->where('token', $token)
            ->first();

        if (! $invitation) {
            return null;
        }

        UserInvitation::syncExpiredForAccount($invitation->account_id);
        $invitation->refresh();

        if (! $invitation->isUsable() || $invitation->type !== UserInvitation::TYPE_INTERNAL) {
            return null;
        }

        return $invitation;
    }
}
