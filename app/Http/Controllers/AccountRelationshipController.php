<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\UserInvitation;
use App\Services\AccountRelationshipsListingService;
use App\Services\UserInvitationAcceptanceService;
use App\Support\AccountBusinessTypeGate;
use App\Support\CurrentAccountSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class AccountRelationshipController extends Controller
{
    public function __construct(
        private readonly AccountRelationshipsListingService $listing,
    ) {
    }

    public function index(Request $request): View
    {
        $account = AccountBusinessTypeGate::resolveCurrentAccount($request);

        $typeCodes = $account->accountTypes()
            ->where('active', true)
            ->pluck('code');

        $hasProvider = $typeCodes->contains('provider');
        $hasOperator = $typeCodes->contains('operator');
        $hasAgency = $typeCodes->contains('agency');

        abort_unless($hasProvider || $hasOperator || $hasAgency, 403);

        if ($hasProvider && $hasOperator) {
            $as = (string) $request->query('as', '');
            if ($as === '') {
                return view('account.relationships.hub-pick', [
                    'account' => $account,
                ]);
            }

            if ($as === 'provider') {
                return $this->listView($account, 'provider');
            }

            if ($as === 'operator') {
                return $this->listView($account, 'operator');
            }
        }

        if ($hasAgency && ! $hasProvider && ! $hasOperator) {
            return $this->listView($account, 'agency');
        }

        if ($hasProvider) {
            return $this->listView($account, 'provider');
        }

        if ($hasOperator) {
            return $this->listView($account, 'operator');
        }

        return $this->listView($account, 'agency');
    }

    private function listView(Account $account, string $perspective): View
    {
        $typeCodes = $account->accountTypes()
            ->where('active', true)
            ->pluck('code');

        $pendingIncoming = in_array($perspective, ['provider', 'agency'], true)
            ? $this->listing->pendingIncomingExternalInvitations((int) $account->id)
            : collect();

        $operatorTab = null;
        $counterpartKind = null;

        if ($perspective === 'operator') {
            $operatorTab = (string) request()->query('tab', 'providers');
            if (! in_array($operatorTab, ['providers', 'agencies'], true)) {
                $operatorTab = 'providers';
            }

            $counterpartKind = $operatorTab === 'agencies' ? 'agency' : 'provider';
        }

        return view('account.relationships.index', [
            'account' => $account,
            'accountLabel' => $this->listing->accountDisplayName($account),
            'perspective' => $perspective,
            'rows' => $this->listing->forAccount((int) $account->id, $perspective, $counterpartKind),
            'pendingIncomingInvitations' => $pendingIncoming,
            'showRoleColumn' => false,
            'showHubBack' => $typeCodes->contains('provider') && $typeCodes->contains('operator'),
            'operatorTab' => $operatorTab,
        ]);
    }

    public function acceptIncomingInvitation(
        Request $request,
        UserInvitation $invitation,
        UserInvitationAcceptanceService $acceptanceService,
    ): RedirectResponse {
        $user = $request->user();
        abort_unless($user !== null, 403);

        $accountId = CurrentAccountSession::accountId($request);
        abort_unless($accountId !== null, 403);

        abort_unless($this->canManageIncomingInvitation($invitation, (int) $accountId, $user), 403);

        if (! $acceptanceService->acceptExternalForExistingUser($invitation, $user)) {
            return redirect()
                ->route('account.relationships.index', $this->relationshipsIndexParams($request))
                ->withErrors(['invitation' => __('invitations.accept_not_available')]);
        }

        return redirect()
            ->route('account.relationships.index', $this->relationshipsIndexParams($request))
            ->with('status', __('invitations.accepted_relationship'));
    }

    public function declineIncomingInvitation(
        Request $request,
        UserInvitation $invitation,
    ): RedirectResponse {
        $user = $request->user();
        abort_unless($user !== null, 403);

        $accountId = CurrentAccountSession::accountId($request);
        abort_unless($accountId !== null, 403);

        abort_unless($this->canManageIncomingInvitation($invitation, (int) $accountId, $user), 403);

        if ($invitation->status !== UserInvitation::STATUS_PENDING || ! $invitation->isUsable()) {
            return redirect()
                ->route('account.relationships.index', $this->relationshipsIndexParams($request))
                ->withErrors(['invitation' => __('invitations.not_pending')]);
        }

        $invitation->forceFill([
            'status' => UserInvitation::STATUS_DECLINED,
            'declined_at' => now(),
        ])->save();

        return redirect()
            ->route('account.relationships.index', $this->relationshipsIndexParams($request))
            ->with('status', __('invitations.declined_relationship'));
    }

    /**
     * @return array<string, string>
     */
    private function relationshipsIndexParams(Request $request): array
    {
        $as = (string) $request->query('as', '');
        if ($as === 'provider' || $as === 'operator' || $as === 'agency') {
            return ['as' => $as];
        }

        return [];
    }

    private function canManageIncomingInvitation(UserInvitation $invitation, int $accountId, \App\Models\User $user): bool
    {
        if ($invitation->type !== UserInvitation::TYPE_EXTERNAL) {
            return false;
        }

        if ((int) $invitation->invited_account_id !== $accountId) {
            return false;
        }

        $invitationEmail = Str::lower((string) $invitation->email);
        $userEmail = Str::lower((string) $user->email);

        return $invitationEmail !== '' && $invitationEmail === $userEmail;
    }
}
