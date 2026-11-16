<?php

namespace App\Support;

use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Sends the user who just accepted an existing-company invitation
 * to that company's commercial relationships, with the session switched there.
 */
final class ExternalInvitationAcceptanceRedirect
{
    public static function toRelationships(Request $request, User $user, UserInvitation $invitation): RedirectResponse
    {
        $accountId = (int) ($invitation->invited_account_id ?? 0);
        if ($accountId > 0 && $user->belongsToAccount($accountId)) {
            CurrentAccountSession::put($request, $user, $accountId);
        }

        return redirect()
            ->route('account.relationships.index')
            ->with('status', __('invitations.accepted_relationship'));
    }
}
