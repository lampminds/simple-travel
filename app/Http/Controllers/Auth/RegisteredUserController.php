<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\RecordLastLogin;
use App\Http\Middleware\SetPermissionsTeamForRequest;
use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\User;
use App\Models\UserInvitation;
use App\Support\CurrentAccountSession;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\PermissionRegistrar;

class RegisteredUserController extends Controller
{
    private const SESSION_STARTUP_ACCOUNT_ID_AFTER_VERIFY = 'startup_account_id_after_verify';

    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $companyTypes = AccountCategory::query()
            ->byGroup('type')
            ->with('translations')
            ->where('active', true)
            ->ordered()
            ->get()
            ->mapWithKeys(fn ($cat) => [
                $cat->id => ['name' => $cat->name, 'description' => $cat->description ?? ''],
            ]);

        $invitation = null;
        $invitationMode = null;

        if ($request->filled('token')) {
            $token = $request->string('token');
            $candidate = UserInvitation::query()
                ->with('account')
                ->where('token', $token)
                ->first();

            UserInvitation::syncExpiredForAccount($candidate?->account_id);

            if ($candidate) {
                $candidate->refresh();
            }

            if (! $candidate) {
                return view('auth.invitation-unavailable', ['reason' => 'invalid']);
            }

            if (! $candidate->isUsable()) {
                return view('auth.invitation-unavailable', [
                    'reason' => $this->invitationUnavailableReason($candidate),
                ]);
            }

            $invitation = $candidate;
            $invitationMode = $candidate->type;
        }

        return view('auth.signup', [
            'companyTypes' => $companyTypes,
            'invitation' => $invitation,
            'invitationMode' => $invitationMode,
        ]);
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->filled('invitation_token')) {
            $token = $request->string('invitation_token');
            $invitation = UserInvitation::query()
                ->where('token', $token)
                ->first();

            if (! $invitation) {
                return redirect()->route('register', ['token' => $token]);
            }

            UserInvitation::syncExpiredForAccount($invitation->account_id);
            $invitation->refresh();

            if (! $invitation->isUsable()) {
                return redirect()->route('register', ['token' => $token]);
            }

            return $invitation->type === UserInvitation::TYPE_INTERNAL
                ? $this->storeInternalInvitation($request, $invitation)
                : $this->storeExternalInvitation($request, $invitation);
        }

        return $this->storeNewCompany($request);
    }

    /**
     * Default signup: new company + owner user.
     */
    private function storeNewCompany(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'company_name' => [
                'required',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $value = trim((string) $value);
                    if (Account::where('name', $value)->orWhere('commercial_name', $value)->exists()) {
                        $fail(__('validation.custom.company_name.unique'));
                    }
                },
            ],
            'company_type' => [
                'required',
                Rule::exists('cat_account_categories', 'id')->where('group', 'type'),
            ],
        ], [
            'email.unique' => __('validation.custom.email.unique_user'),
        ]);

        $companyName = $request->string('company_name')->trim();
        $nick = $this->uniqueNickFromCompanyName($companyName);

        $newAccountId = DB::transaction(function () use ($request, $companyName, $nick) {
            $account = Account::create([
                'nick' => $nick,
                'name' => $companyName,
                'commercial_name' => $companyName,
                'email' => $request->email,
            ]);

            $account->categories()->attach($request->company_type);

            $user = User::create([
                'name' => Str::title($request->string('name')->trim()),
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->accounts()->attach($account->id);

            app(PermissionRegistrar::class)->setPermissionsTeamId($account->id);
            /*
             * Spatie teams: pivot user_model_has_roles stores account_id = new account.
             * Role resolution uses `user_roles` where name matches and (account_id IS NULL OR account_id = team);
             * see Spatie\Permission\Models\Role::findByParam. Seeded global roles (UserRolesTableSeeder:
             * id 1=admin, 2=owner, 3=user) use account_id NULL, so assignRole('owner') attaches role_id 2 on the pivot.
             */
            $user->assignRole('owner');
            throw_unless($user->fresh()->hasRole('owner'), \RuntimeException::class, 'Registration must assign the owner role for the new account.');

            event(new Registered($user));

            Auth::login($user);

            return $account->id;
        });

        return $this->finishRegistrationSession($request, $newAccountId, welcomeCompanyAfterVerify: true);
    }

    /**
     * Join an existing account (employee); role "user".
     */
    private function storeInternalInvitation(Request $request, UserInvitation $invitation): RedirectResponse
    {
        $request->validate([
            'invitation_token' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => 'required|string|confirmed|min:8',
        ], [
            'email.unique' => __('validation.custom.email.unique_user'),
        ]);

        $accountId = DB::transaction(function () use ($request, $invitation) {
            $account = Account::query()->findOrFail($invitation->account_id);

            $registeredEmail = Str::lower($request->string('email')->trim());

            $user = User::create([
                'name' => Str::title($request->string('name')->trim()),
                'email' => $registeredEmail,
                'password' => Hash::make($request->password),
            ]);

            $user->accounts()->attach($account->id);

            app(PermissionRegistrar::class)->setPermissionsTeamId($account->id);
            $user->assignRole('user');
            throw_unless($user->fresh()->hasRole('user'), \RuntimeException::class, 'Invitation registration must assign the user role.');

            $invitation->forceFill([
                'email' => $registeredEmail,
                'status' => UserInvitation::STATUS_ACCEPTED,
                'accepted_at' => now(),
            ])->save();

            event(new Registered($user));

            Auth::login($user);

            return $account->id;
        });

        return $this->finishRegistrationSession($request, $accountId);
    }

    /**
     * Signup with new company but linked to an external invitation (trial for a colleague).
     */
    private function storeExternalInvitation(Request $request, UserInvitation $invitation): RedirectResponse
    {
        $request->validate([
            'invitation_token' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => 'required|string|confirmed|min:8',
            'company_name' => [
                'required',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $value = trim((string) $value);
                    if (Account::where('name', $value)->orWhere('commercial_name', $value)->exists()) {
                        $fail(__('validation.custom.company_name.unique'));
                    }
                },
            ],
            'company_type' => [
                'required',
                Rule::exists('cat_account_categories', 'id')->where('group', 'type'),
            ],
        ], [
            'email.unique' => __('validation.custom.email.unique_user'),
        ]);

        $companyName = $request->string('company_name')->trim();
        $nick = $this->uniqueNickFromCompanyName($companyName);

        $newAccountId = DB::transaction(function () use ($request, $companyName, $nick, $invitation) {
            $email = Str::lower($request->string('email')->trim());

            $account = Account::create([
                'nick' => $nick,
                'name' => $companyName,
                'commercial_name' => $companyName,
                'email' => $email,
            ]);

            $account->categories()->attach($request->company_type);

            $user = User::create([
                'name' => Str::title($request->string('name')->trim()),
                'email' => $email,
                'password' => Hash::make($request->password),
            ]);

            $user->accounts()->attach($account->id);

            app(PermissionRegistrar::class)->setPermissionsTeamId($account->id);
            $user->assignRole('owner');
            throw_unless($user->fresh()->hasRole('owner'), \RuntimeException::class, 'Registration must assign the owner role for the new account.');

            $invitation->forceFill([
                'email' => $email,
                'status' => UserInvitation::STATUS_ACCEPTED,
                'accepted_at' => now(),
            ])->save();

            event(new Registered($user));

            Auth::login($user);

            return $account->id;
        });

        return $this->finishRegistrationSession($request, $newAccountId, welcomeCompanyAfterVerify: true);
    }

    /**
     * Map invitation row to a translation key suffix for {@see invitation-unavailable.blade.php}.
     */
    private function invitationUnavailableReason(UserInvitation $invitation): string
    {
        return match ($invitation->status) {
            UserInvitation::STATUS_REVOKED => 'revoked',
            UserInvitation::STATUS_EXPIRED => 'expired',
            UserInvitation::STATUS_DECLINED => 'declined',
            UserInvitation::STATUS_ACCEPTED => 'accepted',
            UserInvitation::STATUS_PENDING => 'expired',
            default => 'invalid',
        };
    }

    private function finishRegistrationSession(Request $request, int $newAccountId, bool $welcomeCompanyAfterVerify = false): RedirectResponse
    {
        $request->session()->put(RecordLastLogin::SESSION_KEY, true);
        CurrentAccountSession::put($request, $request->user(), $newAccountId);
        $request->session()->forget(SetPermissionsTeamForRequest::SESSION_REQUIRES_ACCOUNT_SELECTION);

        if ($welcomeCompanyAfterVerify) {
            $request->session()->put('welcome_company_after_verify', true);
            $request->session()->put(self::SESSION_STARTUP_ACCOUNT_ID_AFTER_VERIFY, $newAccountId);
        }

        if (! $request->session()->has('locale')) {
            $request->session()->put('locale', config('app.locale'));
        }

        return redirect()->route('verification.notice');
    }

    /**
     * Generate a unique nick for the account from the company name.
     */
    private function uniqueNickFromCompanyName(string $companyName): string
    {
        $base = Str::slug(Str::limit($companyName, 35));
        $base = $base ?: 'empresa';
        $nick = $base;
        $suffix = 0;
        while (Account::where('nick', $nick)->exists()) {
            $suffix++;
            $nick = $base.'-'.$suffix;
        }

        return $nick;
    }
}
