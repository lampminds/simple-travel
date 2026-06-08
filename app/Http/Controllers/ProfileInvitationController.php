<?php

namespace App\Http\Controllers;

use App\Models\AccountPerson;
use App\Models\ContactDepartment;
use App\Models\ContactPosition;
use App\Models\Person;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInvitation;
use App\Notifications\UserInvitationNotification;
use App\Services\ExternalCompanyInvitationService;
use App\Services\PendingInvitationUserCleanup;
use App\Services\ReplicateDefaultRolesToAccountService;
use App\Support\CurrentAccountSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\PermissionRegistrar;

class ProfileInvitationController extends Controller
{
    /** @var list<string> */
    private const INVITATION_STATUS_FILTERS = [
        'all',
        UserInvitation::STATUS_PENDING,
        UserInvitation::STATUS_ACCEPTED,
        UserInvitation::STATUS_DECLINED,
        UserInvitation::STATUS_EXPIRED,
        UserInvitation::STATUS_REVOKED,
    ];

    public function __construct(
        private readonly ParameterReaderController $parameterReader,
        private readonly ExternalCompanyInvitationService $externalCompanyInvitation,
    ) {}

    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('account.invitations.employee');
    }

    /**
     * Employee invitations (internal users for current account).
     */
    public function employee(Request $request): View|RedirectResponse
    {
        return $this->renderInvitationPage($request, UserInvitation::TYPE_INTERNAL);
    }

    /**
     * Company invitations (external trial with new company).
     */
    public function company(Request $request): View|RedirectResponse
    {
        return $this->renderInvitationPage($request, UserInvitation::TYPE_EXTERNAL);
    }

    /**
     * Invitations management for current active account in session.
     */
    private function renderInvitationPage(Request $request, string $invitationType): View|RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('account.dashboard');
        }

        $accountId = CurrentAccountSession::accountId($request);
        if (! $accountId) {
            return redirect()->route('account.dashboard');
        }

        UserInvitation::syncExpiredForAccount($accountId);

        $statusFilter = $request->has('status')
            ? (string) $request->query('status')
            : $this->defaultStatusFilter($accountId, $invitationType);
        if (! in_array($statusFilter, self::INVITATION_STATUS_FILTERS, true)) {
            $statusFilter = UserInvitation::STATUS_PENDING;
        }

        $query = UserInvitation::query()
            ->where('account_id', $accountId)
            ->where('type', $invitationType)
            ->with([
                'invitedBy',
                'role',
                'accountInviting',
                'invitedAccount.accountTypes',
                'establishedRelationship.providerAccount.accountTypes',
            ])
            ->orderByDesc('id');

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $invitations = $query->get();

        $assignableRoles = null;
        $contactDepartments = null;
        $contactPositions = null;

        if ($invitationType === UserInvitation::TYPE_INTERNAL) {
            $this->ensureAssignableRolesExistForAccount((int) $accountId);
            $assignableRoles = getAccountRoles((int) $accountId, ['guest', 'admin']);
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
        }

        return view('account.invitations', [
            'invitations' => $invitations,
            'invitationExpirationDays' => $this->parameterReader->invitationExpirationDays($accountId),
            'maxInvitationsRetries' => $this->parameterReader->maxInvitationsRetries($accountId),
            'statusFilter' => $statusFilter,
            'invitationType' => $invitationType,
            'assignableRoles' => $assignableRoles,
            'contactDepartments' => $contactDepartments,
            'contactPositions' => $contactPositions,
            'indexRoute' => $invitationType === UserInvitation::TYPE_INTERNAL
                ? 'account.invitations.employee'
                : 'account.invitations.company',
            'storeRoute' => $invitationType === UserInvitation::TYPE_INTERNAL
                ? 'account.invitations.store_employee'
                : 'account.invitations.store_company',
            'targetAccountChoices' => $invitationType === UserInvitation::TYPE_EXTERNAL
                ? session('external_invite_account_choices', [])
                : [],
        ]);
    }

    /**
     * Resolve the initial status filter when the request has no explicit status query.
     *
     * The default can be configured via parameter
     * `default_invitation_status` (account override first, then global/default value).
     */
    private function defaultStatusFilter(int $accountId, string $invitationType): string
    {
        $configuredStatus = $this->parameterReader->getRawValue('default_invitation_status', $accountId);
        if ($configuredStatus === null) {
            return UserInvitation::STATUS_PENDING;
        }

        $normalizedStatus = trim($configuredStatus);

        return in_array($normalizedStatus, self::INVITATION_STATUS_FILTERS, true)
            ? $normalizedStatus
            : UserInvitation::STATUS_PENDING;
    }

    /**
     * Tenant accounts need cloned role rows before assignable roles appear in the UI.
     */
    private function ensureAssignableRolesExistForAccount(int $accountId): void
    {
        if ($accountId < 1) {
            return;
        }

        $platformId = (int) config('permission.platform_account_id', 1);
        if ($accountId === $platformId) {
            return;
        }

        if (Role::query()->where('account_id', $accountId)->exists()) {
            return;
        }

        app(ReplicateDefaultRolesToAccountService::class)->replicateTo($accountId, null, null);
    }

    private function cleanupStaleInternalStubFromRequest(Request $request, int $accountId): void
    {
        $normalized = Str::lower(trim((string) $request->input('email', '')));
        if ($normalized === '') {
            return;
        }

        $user = User::query()->where('email', $normalized)->first();
        if ($user === null || $user->activation_state !== User::ACTIVATION_PENDING_INVITATION) {
            return;
        }

        $usableInvitation = UserInvitation::query()
            ->where('account_id', $accountId)
            ->where('email', $normalized)
            ->where('status', UserInvitation::STATUS_PENDING)
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->exists();

        if ($usableInvitation) {
            return;
        }

        app(PendingInvitationUserCleanup::class)->deleteOrphanStubForAccountIfStale($user, $accountId);
    }

    /**
     * Store an employee invitation (internal).
     */
    public function storeEmployee(Request $request): RedirectResponse
    {
        return $this->storeByType($request, UserInvitation::TYPE_INTERNAL);
    }

    /**
     * Store a company invitation (external trial).
     */
    public function storeCompany(Request $request): RedirectResponse
    {
        return $this->storeByType($request, UserInvitation::TYPE_EXTERNAL);
    }

    /**
     * Store a new invitation and email the recipient for current active account.
     */
    private function storeByType(Request $request, string $invitationType): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('account.dashboard');
        }

        $accountId = CurrentAccountSession::accountId($request);
        if (! $accountId) {
            return redirect()->route('account.dashboard');
        }

        UserInvitation::syncExpiredForAccount($accountId);

        if ($invitationType === UserInvitation::TYPE_INTERNAL) {
            $this->cleanupStaleInternalStubFromRequest($request, (int) $accountId);
            $this->ensureAssignableRolesExistForAccount((int) $accountId);
            $assignableRoleIds = array_keys(getAccountRoles((int) $accountId, ['guest', 'admin']));
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    function (string $attribute, mixed $value, \Closure $fail) use ($accountId): void {
                        $email = Str::lower(trim((string) $value));
                        $existing = User::query()->where('email', $email)->first();
                        if ($existing === null) {
                            return;
                        }
                        if ($existing->isPendingInvitation()) {
                            $fail(__('invitations.invitee_pending_elsewhere'));

                            return;
                        }
                        if ($existing->belongsToAccount((int) $accountId)) {
                            $fail(__('invitations.already_member'));
                        }
                    },
                ],
                'role_id' => [
                    'required',
                    'integer',
                    Rule::in($assignableRoleIds),
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
            ]);
        } else {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'company_name' => ['nullable', 'string', 'max:255'],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                ],
                'invited_account_id' => ['nullable', 'integer', 'min:1'],
            ]);
        }

        $normalizedInviteEmail = Str::lower(trim((string) $validated['email']));

        $existsPending = UserInvitation::query()
            ->where('account_id', $accountId)
            ->where('email', $normalizedInviteEmail)
            ->where('status', UserInvitation::STATUS_PENDING)
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->exists();

        if ($existsPending) {
            throw ValidationException::withMessages([
                'email' => __('invitations.duplicate_pending'),
            ]);
        }

        $roleId = $invitationType === UserInvitation::TYPE_INTERNAL
            ? (int) $validated['role_id']
            : Role::platformTemplateRoleIdOrFail('owner');

        $expirationDays = $this->parameterReader->invitationExpirationDays($accountId);

        if ($invitationType === UserInvitation::TYPE_INTERNAL) {
            $displayName = Str::title(trim((string) $validated['name']));
            $email = Str::lower(trim((string) $validated['email']));
            $departmentId = (int) $validated['contact_department_id'];
            $positionId = (int) $validated['contact_position_id'];

            $invitation = DB::transaction(function () use (
                $accountId,
                $user,
                $invitationType,
                $roleId,
                $expirationDays,
                $displayName,
                $email,
                $departmentId,
                $positionId,
            ) {
                $role = Role::query()
                    ->where('account_id', $accountId)
                    ->whereKey($roleId)
                    ->firstOrFail();

                $existingInvitee = User::query()->where('email', $email)->first();

                if ($existingInvitee !== null) {
                    $person = $existingInvitee->persons()->orderBy('persons.id')->first();
                    if ($person === null) {
                        $person = Person::query()->create([
                            'name' => $displayName,
                        ]);
                        $existingInvitee->persons()->attach($person->id);
                    }

                    $existingInvitee->accounts()->syncWithoutDetaching([(int) $accountId]);

                    app(PermissionRegistrar::class)->setPermissionsTeamId($accountId);
                    $existingInvitee->assignRole($role);
                    throw_unless(
                        $existingInvitee->fresh()->hasRole($role->name),
                        \RuntimeException::class,
                        'Employee invitation must assign the selected role.',
                    );

                    AccountPerson::query()->updateOrCreate(
                        [
                            'account_id' => $accountId,
                            'person_id' => $person->id,
                            'link_type' => AccountPerson::LINK_MEMBER,
                        ],
                        [
                            'contact_department_id' => $departmentId,
                            'contact_position_id' => $positionId,
                            'is_primary' => false,
                            'is_active' => true,
                            'is_public_contact' => false,
                            'is_preferred_contact_mode' => false,
                        ],
                    );

                    return UserInvitation::query()->create([
                        'account_id' => $accountId,
                        'account_inviting' => $accountId,
                        'name' => $displayName,
                        'email' => $email,
                        'role_id' => $roleId,
                        'token' => Str::random(64),
                        'send_attempts' => 1,
                        'expires_at' => now()->addDays($expirationDays),
                        'invited_by' => $user->id,
                        'invited_user_id' => $existingInvitee->id,
                        'invited_person_id' => $person->id,
                        'type' => $invitationType,
                        'status' => UserInvitation::STATUS_PENDING,
                    ]);
                }

                $person = Person::query()->create([
                    'name' => $displayName,
                ]);

                $invitedUser = User::query()->create([
                    'name' => $displayName,
                    'email' => $email,
                    'password' => Hash::make(Str::random(64)),
                    'activation_state' => User::ACTIVATION_PENDING_INVITATION,
                ]);

                $invitedUser->persons()->attach($person->id);
                $invitedUser->accounts()->attach($accountId);

                app(PermissionRegistrar::class)->setPermissionsTeamId($accountId);
                $invitedUser->assignRole($role);
                throw_unless(
                    $invitedUser->fresh()->hasRole($role->name),
                    \RuntimeException::class,
                    'Employee invitation must assign the selected role.',
                );

                AccountPerson::query()->create([
                    'account_id' => $accountId,
                    'person_id' => $person->id,
                    'link_type' => AccountPerson::LINK_MEMBER,
                    'contact_department_id' => $departmentId,
                    'contact_position_id' => $positionId,
                    'is_primary' => false,
                    'is_active' => true,
                    'is_public_contact' => false,
                    'is_preferred_contact_mode' => false,
                ]);

                return UserInvitation::query()->create([
                    'account_id' => $accountId,
                    'account_inviting' => $accountId,
                    'name' => $displayName,
                    'email' => $email,
                    'role_id' => $roleId,
                    'token' => Str::random(64),
                    'send_attempts' => 1,
                    'expires_at' => now()->addDays($expirationDays),
                    'invited_by' => $user->id,
                    'invited_user_id' => $invitedUser->id,
                    'invited_person_id' => $person->id,
                    'type' => $invitationType,
                    'status' => UserInvitation::STATUS_PENDING,
                ]);
            });
        } else {
            $contactName = Str::title(trim((string) $validated['name']));
            $proposedCompanyName = Str::title(trim((string) ($validated['company_name'] ?? '')));

            $existingActiveUser = $this->externalCompanyInvitation->findActiveUserByEmail($normalizedInviteEmail);

            if ($existingActiveUser === null && $proposedCompanyName === '') {
                throw ValidationException::withMessages([
                    'company_name' => __('invitations.company_name_required'),
                ]);
            }

            if ($existingActiveUser !== null) {
                $selectedAccountId = isset($validated['invited_account_id'])
                    ? (int) $validated['invited_account_id']
                    : null;

                $resolution = $this->externalCompanyInvitation->resolveTargetAccount(
                    $existingActiveUser,
                    $selectedAccountId !== null && $selectedAccountId > 0 ? $selectedAccountId : null,
                );

                if ($resolution['status'] === 'choose_account') {
                    return redirect()
                        ->route('account.invitations.company')
                        ->withInput($request->only(['name', 'email', 'company_name']))
                        ->with(
                            'external_invite_account_choices',
                            $this->externalCompanyInvitation->accountChoicesForForm($resolution['accounts'])
                        );
                }

                if ($resolution['status'] === 'resolved') {
                    $targetAccountId = (int) $resolution['account_id'];

                    if ($this->externalCompanyInvitation->hasDuplicatePending(
                        (int) $accountId,
                        $normalizedInviteEmail,
                        $targetAccountId,
                    )) {
                        throw ValidationException::withMessages([
                            'email' => __('invitations.duplicate_pending'),
                        ]);
                    }

                    $invitation = $this->externalCompanyInvitation->createPendingForExistingUser(
                        operatorAccountId: (int) $accountId,
                        invitedByUserId: (int) $user->id,
                        name: $contactName,
                        email: $normalizedInviteEmail,
                        roleId: $roleId,
                        expirationDays: $expirationDays,
                        invitedUserId: (int) $existingActiveUser->id,
                        invitedAccountId: $targetAccountId,
                        companyName: $proposedCompanyName !== '' ? $proposedCompanyName : null,
                    );

                    $this->externalCompanyInvitation->deliverInvitation($invitation);

                    return redirect()
                        ->route('account.invitations.company')
                        ->with('status', __('invitations.created_existing_user'));
                }
            }

            $invitation = UserInvitation::create([
                'account_id' => $accountId,
                'account_inviting' => $accountId,
                'name' => $contactName,
                'company_name' => $proposedCompanyName,
                'email' => $normalizedInviteEmail,
                'role_id' => $roleId,
                'token' => Str::random(64),
                'send_attempts' => 1,
                'expires_at' => now()->addDays($expirationDays),
                'invited_by' => $user->id,
                'type' => $invitationType,
                'status' => UserInvitation::STATUS_PENDING,
            ]);

            $this->externalCompanyInvitation->deliverInvitation($invitation);
        }

        if ($invitationType === UserInvitation::TYPE_INTERNAL) {
            Notification::route('mail', $invitation->email)
                ->notify(new UserInvitationNotification($invitation));
        }

        return redirect()
            ->route($invitationType === UserInvitation::TYPE_INTERNAL ? 'account.invitations.employee' : 'account.invitations.company')
            ->with('status', __('invitations.created'));
    }

    /**
     * Force-revoke a pending invitation for current active account.
     */
    public function revoke(Request $request, UserInvitation $invitation): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('account.dashboard');
        }

        $accountId = CurrentAccountSession::accountId($request);
        if (! $accountId || (int) $invitation->account_id !== (int) $accountId) {
            return redirect()->route('account.dashboard');
        }

        if ($invitation->status !== UserInvitation::STATUS_PENDING) {
            return redirect()
                ->route($invitation->type === UserInvitation::TYPE_EXTERNAL ? 'account.invitations.company' : 'account.invitations.employee')
                ->with('status', __('invitations.not_pending'));
        }

        $invitation->markRevoked();

        app(PendingInvitationUserCleanup::class)->deleteStubForInvitation($invitation);

        $returnStatus = $request->input('return_status', UserInvitation::STATUS_PENDING);
        if (! in_array($returnStatus, self::INVITATION_STATUS_FILTERS, true)) {
            $returnStatus = UserInvitation::STATUS_PENDING;
        }

        return redirect()
            ->route(
                $invitation->type === UserInvitation::TYPE_EXTERNAL ? 'account.invitations.company' : 'account.invitations.employee',
                ['status' => $returnStatus]
            )
            ->with('status', __('invitations.revoked'));
    }

    /**
     * Resend a pending invitation email while respecting max retry limit.
     */
    public function resend(Request $request, UserInvitation $invitation): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('account.dashboard');
        }

        $accountId = CurrentAccountSession::accountId($request);
        if (! $accountId || (int) $invitation->account_id !== (int) $accountId) {
            return redirect()->route('account.dashboard');
        }

        if ($invitation->status !== UserInvitation::STATUS_PENDING) {
            return redirect()
                ->route($invitation->type === UserInvitation::TYPE_EXTERNAL ? 'account.invitations.company' : 'account.invitations.employee')
                ->with('status', __('invitations.not_pending'));
        }

        $maxRetries = $this->parameterReader->maxInvitationsRetries($accountId);
        $currentAttempts = (int) ($invitation->send_attempts ?? 1);
        if ($currentAttempts >= $maxRetries) {
            return redirect()
                ->route($invitation->type === UserInvitation::TYPE_EXTERNAL ? 'account.invitations.company' : 'account.invitations.employee')
                ->withErrors(['email' => __('invitations.max_retries_reached')]);
        }

        Notification::route('mail', $invitation->email)
            ->notify(new UserInvitationNotification($invitation));

        $invitation->forceFill([
            'send_attempts' => $currentAttempts + 1,
            'invited_by' => $user->id,
        ])->save();

        $returnStatus = $request->input('return_status', UserInvitation::STATUS_PENDING);
        if (! in_array($returnStatus, self::INVITATION_STATUS_FILTERS, true)) {
            $returnStatus = UserInvitation::STATUS_PENDING;
        }

        return redirect()
            ->route(
                $invitation->type === UserInvitation::TYPE_EXTERNAL ? 'account.invitations.company' : 'account.invitations.employee',
                ['status' => $returnStatus]
            )
            ->with('status', __('invitations.resent'));
    }

}
