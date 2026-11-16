<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserInvitation;
use App\Services\ProviderSupplyInvitationService;
use App\Services\UserInvitationAcceptanceService;
use App\Support\AccountBusinessTypeGate;
use App\Support\CurrentAccountSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AccountProviderSupplyController extends Controller
{
    public function __construct(
        private readonly ProviderSupplyInvitationService $supplyInvitations,
    ) {
    }

    public function index(Request $request): RedirectResponse
    {
        AccountBusinessTypeGate::assertProviderAccount($request);

        return redirect()->route('account.relationships.index');
    }

    public function createInvite(Request $request): View
    {
        $account = AccountBusinessTypeGate::assertProviderAccount($request);

        return view('account.provider-supply.invite', [
            'account' => $account,
            'targetAccountChoices' => session('provider_supply_account_choices', []),
        ]);
    }

    public function storeInvite(Request $request): RedirectResponse
    {
        $account = AccountBusinessTypeGate::assertProviderAccount($request);
        $user = $request->user();
        abort_unless($user !== null, 401);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'invited_account_id' => ['nullable', 'integer', 'min:1'],
        ]);

        $result = $this->supplyInvitations->send(
            buyerAccountId: (int) $account->id,
            invitedByUserId: (int) $user->id,
            name: (string) $validated['name'],
            email: (string) $validated['email'],
            companyName: isset($validated['company_name']) ? (string) $validated['company_name'] : null,
            selectedAccountId: isset($validated['invited_account_id']) ? (int) $validated['invited_account_id'] : null,
        );

        if ($result['outcome'] === ProviderSupplyInvitationService::OUTCOME_CHOOSE) {
            return redirect()
                ->route('account.provider-supply.invite')
                ->withInput($request->only(['name', 'email', 'company_name']))
                ->with('provider_supply_account_choices', $result['choices'] ?? []);
        }

        return redirect()
            ->route('account.relationships.index')
            ->with('status', __('account.provider_supply.invite_sent'));
    }

    public function acceptIncoming(
        Request $request,
        UserInvitation $invitation,
        UserInvitationAcceptanceService $acceptanceService,
    ): RedirectResponse {
        $account = AccountBusinessTypeGate::assertProviderAccount($request);
        $user = $request->user();
        abort_unless($user !== null, 401);
        $this->assertIncomingSupplyInvitation($invitation, (int) $account->id, $user);

        if (! $acceptanceService->acceptExternalForExistingUser($invitation, $user)) {
            return redirect()
                ->route('account.relationships.index')
                ->withErrors(['invitation' => __('invitations.accept_not_available')]);
        }

        return redirect()
            ->route('account.relationships.index')
            ->with('status', __('account.provider_supply.invite_accepted'));
    }

    public function declineIncoming(Request $request, UserInvitation $invitation): RedirectResponse
    {
        $account = AccountBusinessTypeGate::assertProviderAccount($request);
        $user = $request->user();
        abort_unless($user !== null, 401);
        $this->assertIncomingSupplyInvitation($invitation, (int) $account->id, $user);

        if ($invitation->status !== UserInvitation::STATUS_PENDING || ! $invitation->isUsable()) {
            return redirect()
                ->route('account.relationships.index')
                ->withErrors(['invitation' => __('invitations.not_pending')]);
        }

        $invitation->forceFill([
            'status' => UserInvitation::STATUS_DECLINED,
            'declined_at' => now(),
        ])->save();

        return redirect()
            ->route('account.relationships.index')
            ->with('status', __('invitations.declined_relationship'));
    }

    public function revokeOutgoing(Request $request, UserInvitation $invitation): RedirectResponse
    {
        $account = AccountBusinessTypeGate::assertProviderAccount($request);
        abort_unless((int) $invitation->account_id === (int) $account->id, 403);
        abort_unless($invitation->purpose === UserInvitation::PURPOSE_PROVIDER_SUPPLY, 404);

        if ($invitation->status !== UserInvitation::STATUS_PENDING) {
            return redirect()
                ->route('account.relationships.index')
                ->withErrors(['invitation' => __('invitations.not_pending')]);
        }

        $invitation->markRevoked();

        return redirect()
            ->route('account.relationships.index')
            ->with('status', __('invitations.revoked'));
    }

    private function assertIncomingSupplyInvitation(UserInvitation $invitation, int $accountId, User $user): void
    {
        abort_unless($invitation->type === UserInvitation::TYPE_EXTERNAL, 404);
        abort_unless($invitation->purpose === UserInvitation::PURPOSE_PROVIDER_SUPPLY, 404);
        abort_unless((int) $invitation->invited_account_id === $accountId, 403);
        abort_unless($user->belongsToAccount($accountId), 403);
        abort_unless(CurrentAccountSession::accountId(request()) === $accountId, 403);
    }
}
