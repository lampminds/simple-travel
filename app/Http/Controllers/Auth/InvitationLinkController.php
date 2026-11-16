<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\UserInvitationAcceptanceService;
use App\Support\CurrentAccountSession;
use App\Support\ExternalInvitationAcceptanceRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\PermissionRegistrar;

class InvitationLinkController extends Controller
{
    public function __invoke(Request $request, string $token, UserInvitationAcceptanceService $acceptanceService): RedirectResponse|View
    {
        $invitation = $this->findInvitation($token);
        if (! $invitation) {
            return redirect()->route('register', ['token' => $token]);
        }

        $user = $request->user();
        if ($user instanceof User) {
            return $this->acceptForAuthenticatedUser($request, $invitation, $user, $acceptanceService);
        }

        return $this->redirectGuest($invitation);
    }

    /**
     * Drop the current login and reopen the same invitation as a guest.
     */
    public function leave(Request $request, string $token): RedirectResponse
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('invitations.accept', ['token' => $token]);
    }

    /**
     * Move the session into the invited company and accept the link.
     */
    public function switchAccount(Request $request, string $token, UserInvitationAcceptanceService $acceptanceService): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        $invitation = $this->findInvitation($token);
        abort_unless($invitation instanceof UserInvitation, 404);

        $targetId = (int) ($invitation->invited_account_id ?? 0);
        abort_unless($targetId > 0 && $user->belongsToAccount($targetId), 403);

        if (! $invitation->isUsable()) {
            return redirect()->route('invitations.accept', ['token' => $token]);
        }

        CurrentAccountSession::put($request, $user, $targetId);

        if (! $acceptanceService->acceptExternalForExistingUser($invitation->refresh(), $user)) {
            return redirect()
                ->route('account.relationships.index')
                ->withErrors(['invitation' => __('invitations.accept_not_available')]);
        }

        return ExternalInvitationAcceptanceRedirect::toRelationships($request, $user, $invitation);
    }

    private function acceptForAuthenticatedUser(
        Request $request,
        UserInvitation $invitation,
        User $user,
        UserInvitationAcceptanceService $acceptanceService,
    ): RedirectResponse|View {
        if ($invitation->status !== UserInvitation::STATUS_PENDING || ! $invitation->isUsable()) {
            return view('auth.invitation-unavailable', [
                'reason' => $this->unavailableReason($invitation),
            ]);
        }

        if ($invitation->type === UserInvitation::TYPE_INTERNAL) {
            if ($acceptanceService->acceptInternalForExistingUser($invitation, $user)) {
                return redirect()
                    ->route('account.dashboard')
                    ->with('status', __('invitations.internal_accepted_existing'));
            }

            return $this->sessionChoice($invitation, $user, 'leave');
        }

        if ($acceptanceService->acceptExternalForExistingUser($invitation, $user)) {
            return ExternalInvitationAcceptanceRedirect::toRelationships($request, $user, $invitation);
        }

        $targetId = (int) ($invitation->invited_account_id ?? 0);
        if ($targetId > 0 && $user->belongsToAccount($targetId)) {
            return $this->sessionChoice($invitation, $user, 'switch');
        }

        return $this->sessionChoice($invitation, $user, 'leave');
    }

    private function redirectGuest(UserInvitation $invitation): RedirectResponse
    {
        $existingUser = User::query()
            ->whereRaw('LOWER(email) = ?', [Str::lower((string) $invitation->email)])
            ->first();

        if ($existingUser !== null) {
            if (
                $invitation->type === UserInvitation::TYPE_INTERNAL
                && $existingUser->activation_state === User::ACTIVATION_PENDING_INVITATION
                && $invitation->invited_user_id !== null
                && (int) $invitation->invited_user_id === (int) $existingUser->getKey()
                && $invitation->isUsable()
            ) {
                return redirect()
                    ->route('register', ['token' => $invitation->token])
                    ->with('status', __('invitations.register_to_accept'));
            }

            return redirect()
                ->route('login', ['invitation_token' => $invitation->token])
                ->with('status', __('invitations.login_to_accept'));
        }

        return redirect()
            ->route('register', ['token' => $invitation->token])
            ->with('status', __('invitations.register_to_accept'));
    }

    private function sessionChoice(UserInvitation $invitation, User $user, string $mode): View
    {
        $invitation->loadMissing(['invitedAccount', 'accountInviting', 'account']);

        $company = $invitation->invitedAccount ?? $invitation->accountInviting ?? $invitation->account;
        $operator = $invitation->accountInviting ?? $invitation->account;

        return view('auth.invitation-session', [
            'mode' => $mode,
            'token' => (string) $invitation->token,
            'kind' => (string) $invitation->type,
            'currentEmail' => (string) $user->email,
            'inviteEmail' => (string) $invitation->email,
            'companyName' => $this->accountLabel($company),
            'operatorName' => $this->accountLabel($operator),
        ]);
    }

    private function findInvitation(string $token): ?UserInvitation
    {
        $invitation = UserInvitation::query()
            ->where('token', $token)
            ->first();

        if (! $invitation instanceof UserInvitation) {
            return null;
        }

        UserInvitation::syncExpiredForAccount($invitation->account_id);
        $invitation->refresh();

        return $invitation;
    }

    private function unavailableReason(UserInvitation $invitation): string
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

    private function accountLabel(?Account $account): string
    {
        $label = trim((string) ($account?->commercial_name ?: $account?->name ?: $account?->nick ?: ''));

        return $label !== '' ? $label : '—';
    }
}
