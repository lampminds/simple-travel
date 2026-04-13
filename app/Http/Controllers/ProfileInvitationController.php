<?php

namespace App\Http\Controllers;

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

    /**
     * Invitations management (owner only).
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && $user->hasRoleForCurrentAccount('owner'), 403);

        $accountId = $user->currentAccountId();
        abort_unless($accountId, 403);

        UserInvitation::syncExpiredForAccount($accountId);

        $statusFilter = $request->query('status', UserInvitation::STATUS_PENDING);
        if (! in_array($statusFilter, self::INVITATION_STATUS_FILTERS, true)) {
            $statusFilter = UserInvitation::STATUS_PENDING;
        }

        $query = UserInvitation::query()
            ->where('account_id', $accountId)
            ->with('invitedBy')
            ->orderByDesc('id');

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $invitations = $query->get();

        return view('account.invitations', [
            'invitations' => $invitations,
            'invitationExpirationDays' => $this->parameterReader->invitationExpirationDays($accountId),
            'statusFilter' => $statusFilter,
        ]);
    }

    /**
     * Store a new invitation and email the recipient (owner only; account from current team).
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->hasRoleForCurrentAccount('owner'), 403);

        $accountId = $user->currentAccountId();
        abort_unless($accountId, 403);

        UserInvitation::syncExpiredForAccount($accountId);

        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'type' => ['required', Rule::in([UserInvitation::TYPE_INTERNAL, UserInvitation::TYPE_EXTERNAL])],
        ]);

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

        $expirationDays = $this->parameterReader->invitationExpirationDays($accountId);

        $invitation = UserInvitation::create([
            'account_id' => $accountId,
            'email' => Str::lower($validated['email']),
            'token' => Str::random(64),
            'expires_at' => now()->addDays($expirationDays),
            'invited_by' => $user->id,
            'type' => $validated['type'],
            'status' => UserInvitation::STATUS_PENDING,
        ]);

        Notification::route('mail', $invitation->email)
            ->notify(new UserInvitationNotification($invitation));

        return redirect()
            ->route('account.invitations.index')
            ->with('status', __('invitations.created'));
    }

    /**
     * Force-revoke a pending invitation (owner only; same account).
     */
    public function revoke(Request $request, UserInvitation $invitation): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->hasRoleForCurrentAccount('owner'), 403);

        $accountId = $user->currentAccountId();
        abort_unless($accountId && (int) $invitation->account_id === (int) $accountId, 403);

        if ($invitation->status !== UserInvitation::STATUS_PENDING) {
            return redirect()
                ->route('account.invitations.index')
                ->with('status', __('invitations.not_pending'));
        }

        $invitation->markRevoked();

        $returnStatus = $request->input('return_status', UserInvitation::STATUS_PENDING);
        if (! in_array($returnStatus, self::INVITATION_STATUS_FILTERS, true)) {
            $returnStatus = UserInvitation::STATUS_PENDING;
        }

        return redirect()
            ->route('account.invitations.index', ['status' => $returnStatus])
            ->with('status', __('invitations.revoked'));
    }
}
