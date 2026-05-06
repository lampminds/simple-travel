<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\UserInvitationAcceptanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvitationLinkController extends Controller
{
    public function __invoke(Request $request, string $token, UserInvitationAcceptanceService $acceptanceService): RedirectResponse
    {
        $invitation = UserInvitation::query()
            ->where('token', $token)
            ->first();

        if (! $invitation) {
            return redirect()->route('register', ['token' => $token]);
        }

        UserInvitation::syncExpiredForAccount($invitation->account_id);
        $invitation->refresh();

        if ($request->user()) {
            $accepted = $acceptanceService->acceptExternalForExistingUser($invitation, $request->user());

            if ($accepted) {
                return redirect()
                    ->route('account.relationships.index')
                    ->with('status', __('invitations.accepted_relationship'));
            }

            return redirect()
                ->route('account.relationships.index')
                ->with('status', __('invitations.accept_not_available'));
        }

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
}

