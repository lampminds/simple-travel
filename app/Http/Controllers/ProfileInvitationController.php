<?php

namespace App\Http\Controllers;

use App\Support\CurrentAccountSession;
use App\Models\Role;
use App\Models\UserInvitation;
use App\Notifications\UserInvitationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

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

        $statusFilter = $request->query('status', UserInvitation::STATUS_PENDING);
        if (! in_array($statusFilter, self::INVITATION_STATUS_FILTERS, true)) {
            $statusFilter = UserInvitation::STATUS_PENDING;
        }

        $query = UserInvitation::query()
            ->where('account_id', $accountId)
            ->where('type', $invitationType)
            ->with(['invitedBy', 'role', 'accountInviting'])
            ->orderByDesc('id');

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $invitations = $query->get();

        $assignableRoles = $invitationType === UserInvitation::TYPE_INTERNAL
            ? getAccountRoles((int) $accountId, ['guest', 'admin'])
            : null;

        return view('account.invitations', [
            'invitations' => $invitations,
            'invitationExpirationDays' => $this->parameterReader->invitationExpirationDays($accountId),
            'maxInvitationsRetries' => $this->parameterReader->maxInvitationsRetries($accountId),
            'statusFilter' => $statusFilter,
            'invitationType' => $invitationType,
            'assignableRoles' => $assignableRoles,
            'indexRoute' => $invitationType === UserInvitation::TYPE_INTERNAL
                ? 'account.invitations.employee'
                : 'account.invitations.company',
            'storeRoute' => $invitationType === UserInvitation::TYPE_INTERNAL
                ? 'account.invitations.store_employee'
                : 'account.invitations.store_company',
        ]);
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
            $assignableRoleIds = array_keys(getAccountRoles((int) $accountId, ['guest', 'admin']));
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email'),
                ],
                'role_id' => [
                    'required',
                    'integer',
                    Rule::in($assignableRoleIds),
                ],
            ]);
        } else {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email'),
                ],
            ]);
        }

        $existsPending = UserInvitation::query()
            ->where('account_id', $accountId)
            ->where('email', $validated['email'])
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

        $invitation = UserInvitation::create([
            'account_id' => $accountId,
            'account_inviting' => $accountId,
            'name' => Str::title(trim((string) $validated['name'])),
            'email' => Str::lower($validated['email']),
            'role_id' => $roleId,
            'token' => Str::random(64),
            'send_attempts' => 1,
            'expires_at' => now()->addDays($expirationDays),
            'invited_by' => $user->id,
            'type' => $invitationType,
            'status' => UserInvitation::STATUS_PENDING,
        ]);

        Notification::route('mail', $invitation->email)
            ->notify(new UserInvitationNotification($invitation));

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
