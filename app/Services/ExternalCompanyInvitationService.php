<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\User;
use App\Models\UserInvitation;
use App\Notifications\UserInvitationNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * External (company) invitations when the invitee email already belongs to an active user.
 */
final class ExternalCompanyInvitationService
{
    public function __construct(
        private readonly AccountNotificationService $accountNotifications,
        private readonly AccountRelationshipsListingService $accountLabels,
    ) {
    }

    public function findActiveUserByEmail(string $email): ?User
    {
        $normalized = Str::lower(trim($email));
        if ($normalized === '') {
            return null;
        }

        return User::query()
            ->whereRaw('LOWER(email) = ?', [$normalized])
            ->where('activation_state', User::ACTIVATION_ACTIVE)
            ->first();
    }

    /**
     * @return Collection<int, Account>
     */
    public function memberAccountsForUser(User $user): Collection
    {
        return $user->accounts()
            ->orderBy('accounts.id')
            ->get();
    }

    /**
     * @return array{
     *     status: 'none'|'resolved'|'choose_account',
     *     account_id?: int,
     *     accounts?: Collection<int, Account>
     * }
     */
    public function resolveTargetAccount(User $user, ?int $selectedAccountId): array
    {
        $accounts = $this->memberAccountsForUser($user);

        if ($accounts->isEmpty()) {
            return ['status' => 'none'];
        }

        if ($selectedAccountId !== null && $selectedAccountId > 0) {
            $match = $accounts->firstWhere('id', $selectedAccountId);
            if ($match === null) {
                throw ValidationException::withMessages([
                    'invited_account_id' => __('invitations.target_account_invalid'),
                ]);
            }

            return ['status' => 'resolved', 'account_id' => (int) $match->id];
        }

        if ($accounts->count() === 1) {
            return ['status' => 'resolved', 'account_id' => (int) $accounts->first()->id];
        }

        return ['status' => 'choose_account', 'accounts' => $accounts];
    }

    /**
     * @return list<array{id: int, label: string}>
     */
    public function accountChoicesForForm(Collection $accounts): array
    {
        return $accounts
            ->map(function (Account $account): array {
                $types = $account->accountTypes()
                    ->where('active', true)
                    ->pluck('code')
                    ->map(function (string $code): string {
                        $key = 'account.relationships.role.'.$code;
                        $label = __($key);

                        return $label !== $key ? (string) $label : Str::title($code);
                    })
                    ->filter()
                    ->implode(', ');

                $name = $this->accountLabels->accountDisplayName($account);
                $label = $types !== '' ? "{$name} ({$types})" : $name;

                return ['id' => (int) $account->id, 'label' => $label];
            })
            ->values()
            ->all();
    }

    public function hasDuplicatePending(
        int $operatorAccountId,
        string $email,
        ?int $invitedAccountId = null,
    ): bool {
        $normalized = Str::lower(trim($email));

        $query = UserInvitation::query()
            ->where('account_id', $operatorAccountId)
            ->where('type', UserInvitation::TYPE_EXTERNAL)
            ->where('status', UserInvitation::STATUS_PENDING)
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->where(function ($q) use ($normalized, $invitedAccountId): void {
                $q->where('email', $normalized);
                if ($invitedAccountId !== null && $invitedAccountId > 0) {
                    $q->orWhere('invited_account_id', $invitedAccountId);
                }
            });

        return $query->exists();
    }

    public function createPendingForExistingUser(
        int $operatorAccountId,
        int $invitedByUserId,
        string $name,
        string $email,
        int $roleId,
        int $expirationDays,
        int $invitedUserId,
        int $invitedAccountId,
        ?string $companyName = null,
    ): UserInvitation {
        $resolvedCompanyName = trim((string) $companyName);
        if ($resolvedCompanyName === '') {
            $account = Account::query()->find($invitedAccountId);
            $resolvedCompanyName = trim((string) ($account?->commercial_name ?? $account?->name ?? ''));
        }

        return UserInvitation::query()->create([
            'account_id' => $operatorAccountId,
            'account_inviting' => $operatorAccountId,
            'invited_account_id' => $invitedAccountId,
            'name' => Str::title(trim($name)),
            'company_name' => $resolvedCompanyName !== '' ? Str::title($resolvedCompanyName) : null,
            'email' => Str::lower(trim($email)),
            'role_id' => $roleId,
            'token' => Str::random(64),
            'send_attempts' => 1,
            'expires_at' => now()->addDays($expirationDays),
            'invited_by' => $invitedByUserId,
            'invited_user_id' => $invitedUserId,
            'type' => UserInvitation::TYPE_EXTERNAL,
            'status' => UserInvitation::STATUS_PENDING,
        ]);
    }

    public function deliverInvitation(UserInvitation $invitation, bool $sendEmail = true): void
    {
        $invitation->loadMissing(['account', 'accountInviting', 'invitedAccount']);

        if ($invitation->invited_account_id !== null) {
            $this->accountNotifications->createForExternalInvitationReceived($invitation);
        }

        if ($sendEmail) {
            Notification::route('mail', $invitation->email)
                ->notify(new UserInvitationNotification($invitation));
        }
    }
}
