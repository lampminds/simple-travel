<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetPermissionsTeamForRequest;
use App\Models\Account;
use App\Models\AccountType;
use App\Models\AccountPerson;
use App\Models\ContactDepartment;
use App\Models\ContactPosition;
use App\Models\Person;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\ReplicateDefaultRolesToAccountService;
use App\Services\AccountNotificationService;
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
use Illuminate\Validation\ValidationException;
use Spatie\Permission\PermissionRegistrar;

class RegisteredUserController extends Controller
{
    private const SESSION_STARTUP_ACCOUNT_ID_AFTER_VERIFY = 'startup_account_id_after_verify';

    private const SESSION_STARTUP_EXTERNAL_INVITATION_ID_AFTER_VERIFY = 'startup_external_invitation_id_after_verify';

    /** Business types (cat_account_types.id) chosen at signup; reapplied on email verify if needed. */
    private const SESSION_STARTUP_COMPANY_TYPE_CATEGORY_IDS_AFTER_VERIFY = 'startup_company_type_category_ids_after_verify';

    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $companyTypes = AccountType::query()
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
                ->with(['account', 'accountInviting', 'role', 'invitedBy'])
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

        $contactDepartments = ContactDepartment::query()
            ->where('active', true)
            ->orderBy('sort_order')
            ->with(['translations.language.locale'])
            ->get();

        $contactPositions = ContactPosition::query()
            ->where('active', true)
            ->orderBy('sort_order')
            ->with(['translations.language.locale'])
            ->get();

        return view('auth.signup', [
            'companyTypes' => $companyTypes,
            'invitation' => $invitation,
            'invitationMode' => $invitationMode,
            'contactDepartments' => $contactDepartments,
            'contactPositions' => $contactPositions,
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
        $validated = $request->validate([
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
            'company_types' => ['required', 'array', 'min:1'],
            'company_types.*' => [
                'required',
                'integer',
                Rule::exists('cat_account_types', 'id')->where('active', true),
            ],
            'contact_department_id' => [
                'required',
                'integer',
                Rule::exists('cat_contact_departments', 'id')->where('active', true),
            ],
            'contact_position_id' => [
                'required',
                'integer',
                Rule::exists('cat_contact_positions', 'id')->where('active', true),
            ],
        ], [
            'email.unique' => __('validation.custom.email.unique_user'),
        ]);

        $companyName = $request->string('company_name')->trim();
        $nick = $this->uniqueNickFromCompanyName($companyName);
        $companyTypeCategoryIds = collect($validated['company_types'])
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();
        $ownerDisplayName = Str::title($request->string('name')->trim());
        $email = Str::lower($request->string('email')->trim());

        $bundle = $this->registerNewCompanyWithOwnerPerson(
            companyName: $companyName,
            nick: $nick,
            companyTypeCategoryIds: $companyTypeCategoryIds,
            email: $email,
            passwordPlain: $request->string('password')->value(),
            ownerDisplayName: $ownerDisplayName,
            contactDepartmentId: (int) $validated['contact_department_id'],
            contactPositionId: (int) $validated['contact_position_id'],
            invitationToComplete: null,
        );

        $newAccountId = $bundle['account']->id;

        return $this->finishRegistrationSession(
            $request,
            $newAccountId,
            welcomeCompanyAfterVerify: true,
            externalInvitationIdAfterVerify: null,
            companyTypeCategoryIdsAfterVerify: $companyTypeCategoryIds
        );
    }

    /**
     * Join an existing account (employee); role "user".
     */
    private function storeInternalInvitation(Request $request, UserInvitation $invitation): RedirectResponse
    {
        $request->validate([
            'invitation_token' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::in([Str::lower($invitation->email)]),
            ],
            'password' => 'required|string|confirmed|min:8',
        ], [
            'email.in' => __('auth.register.invitation_email_mismatch'),
        ]);

        $accountId = DB::transaction(function () use ($request, $invitation) {
            $account = Account::query()->findOrFail($invitation->account_id);

            $registeredEmail = Str::lower($request->string('email')->trim());
            $registeredName = Str::title($request->string('name')->trim());

            $role = Role::query()
                ->where('account_id', $invitation->account_id)
                ->whereKey($invitation->role_id)
                ->firstOrFail();

            $dispatchRegisteredEvent = true;

            if ($invitation->invited_user_id !== null) {
                $user = User::query()->findOrFail($invitation->invited_user_id);

                if (Str::lower((string) $user->email) !== $registeredEmail) {
                    throw ValidationException::withMessages([
                        'email' => __('auth.register.invitation_email_mismatch'),
                    ]);
                }

                if ($user->isPendingInvitation()) {
                    $user->forceFill([
                        'name' => $registeredName,
                        'password' => Hash::make($request->password),
                        'activation_state' => User::ACTIVATION_ACTIVE,
                    ])->save();

                    $person = $user->persons()->orderBy('persons.id')->first();
                    if ($person !== null) {
                        $person->forceFill(['name' => $registeredName])->save();
                    }
                } elseif ($user->activation_state === User::ACTIVATION_ACTIVE) {
                    $user->forceFill([
                        'name' => $registeredName,
                    ])->save();

                    $dispatchRegisteredEvent = false;
                } else {
                    throw ValidationException::withMessages([
                        'email' => __('invitations.invitation_already_used'),
                    ]);
                }

                app(PermissionRegistrar::class)->setPermissionsTeamId($account->id);
                throw_unless(
                    $user->fresh()->hasRole($role->name),
                    \RuntimeException::class,
                    'Invitation registration must preserve the role stored on the invitation.',
                );
            } else {
                $user = User::query()->create([
                    'name' => $registeredName,
                    'email' => $registeredEmail,
                    'password' => Hash::make($request->password),
                    'activation_state' => User::ACTIVATION_ACTIVE,
                ]);

                $user->accounts()->attach($account->id);

                app(PermissionRegistrar::class)->setPermissionsTeamId($account->id);
                $user->assignRole($role);
                throw_unless(
                    $user->fresh()->hasRole($role->name),
                    \RuntimeException::class,
                    'Invitation registration must assign the role stored on the invitation.',
                );
            }

            $invitation->forceFill([
                'email' => $registeredEmail,
                'name' => $registeredName,
                'status' => UserInvitation::STATUS_ACCEPTED,
                'accepted_at' => now(),
            ])->save();

            if ($dispatchRegisteredEvent) {
                event(new Registered($user));
            }

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
        $validated = $request->validate([
            'invitation_token' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                Rule::in([Str::lower($invitation->email)]),
            ],
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
            'company_types' => ['required', 'array', 'min:1'],
            'company_types.*' => [
                'required',
                'integer',
                Rule::exists('cat_account_types', 'id')->where('active', true),
            ],
            'contact_department_id' => [
                'required',
                'integer',
                Rule::exists('cat_contact_departments', 'id')->where('active', true),
            ],
            'contact_position_id' => [
                'required',
                'integer',
                Rule::exists('cat_contact_positions', 'id')->where('active', true),
            ],
        ], [
            'email.unique' => __('validation.custom.email.unique_user'),
            'email.in' => __('auth.register.invitation_email_mismatch'),
        ]);

        $companyName = $request->string('company_name')->trim();
        $nick = $this->uniqueNickFromCompanyName($companyName);
        $companyTypeCategoryIds = collect($validated['company_types'])
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();
        $ownerDisplayName = Str::title($request->string('name')->trim());
        $email = Str::lower($request->string('email')->trim());

        $bundle = $this->registerNewCompanyWithOwnerPerson(
            companyName: $companyName,
            nick: $nick,
            companyTypeCategoryIds: $companyTypeCategoryIds,
            email: $email,
            passwordPlain: $request->string('password')->value(),
            ownerDisplayName: $ownerDisplayName,
            contactDepartmentId: (int) $validated['contact_department_id'],
            contactPositionId: (int) $validated['contact_position_id'],
            invitationToComplete: $invitation,
        );

        $newAccountId = $bundle['account']->id;

        return $this->finishRegistrationSession(
            $request,
            $newAccountId,
            welcomeCompanyAfterVerify: true,
            externalInvitationIdAfterVerify: (int) $invitation->id,
            companyTypeCategoryIdsAfterVerify: $companyTypeCategoryIds
        );
    }

    /**
     * Create person, user, user_person, account, account_user, default roles, and account_person (owner).
     *
     * @return array{user: User, account: Account, person: Person}
     */
    private function registerNewCompanyWithOwnerPerson(
        string $companyName,
        string $nick,
        array $companyTypeCategoryIds,
        string $email,
        string $passwordPlain,
        string $ownerDisplayName,
        int $contactDepartmentId,
        int $contactPositionId,
        ?UserInvitation $invitationToComplete,
    ): array {
        return DB::transaction(function () use (
            $companyName,
            $nick,
            $companyTypeCategoryIds,
            $email,
            $passwordPlain,
            $ownerDisplayName,
            $contactDepartmentId,
            $contactPositionId,
            $invitationToComplete,
        ) {
            $person = Person::create([
                'name' => $ownerDisplayName,
            ]);

            $user = User::create([
                'name' => $ownerDisplayName,
                'email' => $email,
                'password' => Hash::make($passwordPlain),
            ]);

            $user->persons()->attach($person->id);

            $account = Account::create([
                'nick' => $nick,
                'name' => $companyName,
                'commercial_name' => $companyName,
                'email' => $email,
            ]);

            $user->accounts()->attach($account->id);

            app(PermissionRegistrar::class)->setPermissionsTeamId($account->id);
            app(ReplicateDefaultRolesToAccountService::class)->replicateTo((int) $account->id, null, (int) $user->id);
            $user->assignRole('owner');
            throw_unless(
                $user->fresh()->hasRole('owner'),
                \RuntimeException::class,
                'Registration must assign the owner role for the new account.',
            );

            AccountPerson::create([
                'account_id' => $account->id,
                'person_id' => $person->id,
                'contact_department_id' => $contactDepartmentId,
                'contact_position_id' => $contactPositionId,
                'is_primary' => true,
                'is_active' => true,
                'is_public_contact' => false,
                'is_preferred_contact_mode' => false,
            ]);

            $account->accountTypes()->attach($companyTypeCategoryIds);

            if ($invitationToComplete !== null) {
                $invitationToComplete->forceFill([
                    'email' => $email,
                    'name' => $ownerDisplayName,
                    'status' => UserInvitation::STATUS_ACCEPTED,
                    'accepted_at' => now(),
                ])->save();

                if ($invitationToComplete->type === UserInvitation::TYPE_EXTERNAL) {
                    app(AccountNotificationService::class)->createForExternalInvitationAccepted(
                        invitation: $invitationToComplete,
                        providerAccount: $account,
                        providerAlreadyExisted: false,
                    );
                }
            }

            app(AccountNotificationService::class)->createWelcomeForNewAccount((int) $account->id, $user);

            event(new Registered($user));

            Auth::login($user);

            return [
                'user' => $user,
                'account' => $account,
                'person' => $person,
            ];
        });
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

    private function finishRegistrationSession(
        Request $request,
        int $newAccountId,
        bool $welcomeCompanyAfterVerify = false,
        ?int $externalInvitationIdAfterVerify = null,
        ?array $companyTypeCategoryIdsAfterVerify = null
    ): RedirectResponse
    {
        CurrentAccountSession::put($request, $request->user(), $newAccountId);
        $request->session()->forget(SetPermissionsTeamForRequest::SESSION_REQUIRES_ACCOUNT_SELECTION);

        if ($welcomeCompanyAfterVerify) {
            $request->session()->put('welcome_company_after_verify', true);
            $request->session()->put(self::SESSION_STARTUP_ACCOUNT_ID_AFTER_VERIFY, $newAccountId);

            if ($externalInvitationIdAfterVerify !== null && $externalInvitationIdAfterVerify > 0) {
                $request->session()->put(
                    self::SESSION_STARTUP_EXTERNAL_INVITATION_ID_AFTER_VERIFY,
                    $externalInvitationIdAfterVerify
                );
            }

            if (is_array($companyTypeCategoryIdsAfterVerify) && $companyTypeCategoryIdsAfterVerify !== []) {
                $request->session()->put(
                    self::SESSION_STARTUP_COMPANY_TYPE_CATEGORY_IDS_AFTER_VERIFY,
                    array_values(array_unique(array_map(fn ($id): int => (int) $id, $companyTypeCategoryIdsAfterVerify)))
                );
            }
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
