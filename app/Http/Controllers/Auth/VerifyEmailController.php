<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\UserInvitation;
use App\Services\AccountStartupService;
use App\Services\AccountRelationshipService;
use App\Providers\RouteServiceProvider;
use App\Support\CurrentAccountSession;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class VerifyEmailController extends Controller
{
    private const SESSION_STARTUP_ACCOUNT_ID_AFTER_VERIFY = 'startup_account_id_after_verify';
    private const SESSION_STARTUP_EXTERNAL_INVITATION_ID_AFTER_VERIFY = 'startup_external_invitation_id_after_verify';

    private const SESSION_STARTUP_COMPANY_TYPE_CATEGORY_IDS_AFTER_VERIFY = 'startup_company_type_category_ids_after_verify';

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     */
    public function __invoke(
        EmailVerificationRequest $request,
        AccountStartupService $accountStartupService,
        AccountRelationshipService $accountRelationshipService
    ): RedirectResponse
    {
        $user = $request->user();

        if ($request->user()->hasVerifiedEmail()) {
            $this->ensureRelationshipAfterVerification($request, $accountRelationshipService);

            return redirect()->intended(RouteServiceProvider::HOME)
                ->with('status', __('auth.verification.already_verified'));
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        $startupAccountId = (int) $request->session()->pull(self::SESSION_STARTUP_ACCOUNT_ID_AFTER_VERIFY, 0);
        if ($startupAccountId > 0 && $request->user()->belongsToAccount($startupAccountId)) {
            $accountStartupService->runForNewAccount(
                $startupAccountId,
                (int) $request->user()->id
            );
        }

        $companyTypeCategoryIds = collect((array) $request->session()->pull(self::SESSION_STARTUP_COMPANY_TYPE_CATEGORY_IDS_AFTER_VERIFY, []))
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();
        if ($startupAccountId > 0 && $companyTypeCategoryIds !== [] && $request->user()->belongsToAccount($startupAccountId)) {
            $validIds = AccountCategory::query()
                ->whereIn('id', $companyTypeCategoryIds)
                ->where('group', 'type')
                ->where('active', true)
                ->pluck('id')
                ->map(fn ($id): int => (int) $id)
                ->all();
            if ($validIds !== []) {
                $account = Account::query()->find($startupAccountId);
                if ($account !== null) {
                    $account->categories()->syncWithoutDetaching($validIds);
                    CurrentAccountSession::put($request, $request->user(), $startupAccountId);
                }
            }
        }

        $this->ensureRelationshipAfterVerification($request, $accountRelationshipService, $startupAccountId);

        if ($request->session()->pull('welcome_company_after_verify')) {
            return redirect()
                ->route('welcome.company')
                ->with('status', __('auth.verification.verified'));
        }

        return redirect()->intended(RouteServiceProvider::HOME)
            ->with('status', __('auth.verification.verified'));
    }

    private function ensureRelationshipAfterVerification(
        EmailVerificationRequest $request,
        AccountRelationshipService $accountRelationshipService,
        ?int $providerAccountIdHint = null
    ): void {
        $user = $request->user();
        $providerAccountId = $providerAccountIdHint ?? 0;

        if ($providerAccountId <= 0 || ! $user->belongsToAccount($providerAccountId)) {
            $providerAccountId = (int) ($user->currentAccountId() ?? 0);
        }

        if ($providerAccountId <= 0) {
            $providerAccountId = (int) $user->accounts()->orderByDesc('accounts.id')->value('accounts.id');
        }

        if ($providerAccountId <= 0) {
            return;
        }

        $invitation = null;
        $externalInvitationId = (int) $request->session()->pull(self::SESSION_STARTUP_EXTERNAL_INVITATION_ID_AFTER_VERIFY, 0);
        if ($externalInvitationId > 0) {
            $invitation = UserInvitation::query()->find($externalInvitationId);
        }

        if (! $invitation) {
            $invitation = UserInvitation::query()
                ->where('type', UserInvitation::TYPE_EXTERNAL)
                ->where('status', UserInvitation::STATUS_ACCEPTED)
                ->whereNotNull('accepted_at')
                ->whereRaw('LOWER(email) = ?', [Str::lower((string) $user->email)])
                ->orderByDesc('accepted_at')
                ->first();
        }

        if (! $invitation || $invitation->type !== UserInvitation::TYPE_EXTERNAL) {
            return;
        }

        if (! Account::query()->whereKey($providerAccountId)->exists()) {
            return;
        }

        $accountRelationshipService->approveFromExternalInvitation(
            invitation: $invitation,
            providerAccountId: $providerAccountId,
            approvedByUserId: (int) $user->id
        );
    }
}
