<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountNotification;
use App\Models\User;
use App\Models\UserInvitation;

final class AccountNotificationService
{
    /**
     * Create a shared notification for one account.
     *
     * @param  array<string, mixed>|null  $data
     */
    public function createForAccount(
        int $accountId,
        string $type,
        string $title,
        string $message,
        ?int $recipientUserId = null,
        ?array $data = null,
    ): AccountNotification {
        return AccountNotification::query()->create([
            'account_id' => $accountId,
            'recipient_user_id' => $recipientUserId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Create the default welcome notification after first account registration.
     */
    public function createWelcomeForNewAccount(int $accountId, User $user): AccountNotification
    {
        return $this->createForAccount(
            accountId: $accountId,
            type: 'account_welcome',
            title: (string) __('account.notifications.welcome_title'),
            message: (string) __('account.notifications.welcome_message', ['name' => $user->name]),
            recipientUserId: null,
            data: [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
            ],
        );
    }

    /**
     * Notify target company when an operator sends an external invitation to an existing user.
     */
    public function createForExternalInvitationReceived(UserInvitation $invitation): AccountNotification
    {
        $invitation->loadMissing(['account', 'accountInviting']);
        $operatorAccount = $invitation->accountInviting ?? $invitation->account;
        $operatorName = (string) ($operatorAccount?->commercial_name ?: $operatorAccount?->name ?: '#'.$invitation->account_id);

        return $this->createForAccount(
            accountId: (int) $invitation->invited_account_id,
            type: 'external_invitation_received',
            title: (string) __('account.notifications.external_invitation_received_title', ['company' => $operatorName]),
            message: (string) __('account.notifications.external_invitation_received_message', ['company' => $operatorName]),
            recipientUserId: null,
            data: [
                'invitation_id' => (int) $invitation->id,
                'operator_account_id' => (int) ($operatorAccount?->id ?? $invitation->account_id),
                'operator_account_name' => $operatorName,
            ],
        );
    }

    /**
     * Notify inviter account/user when an external invitation is accepted.
     */
    public function createForExternalInvitationAccepted(
        UserInvitation $invitation,
        Account $providerAccount,
        bool $providerAlreadyExisted,
    ): AccountNotification {
        $operatorAccountId = (int) ($invitation->account_inviting ?: $invitation->account_id);
        $companyName = (string) ($providerAccount->commercial_name ?: $providerAccount->name);

        return $this->createForAccount(
            accountId: $operatorAccountId,
            type: $providerAlreadyExisted
                ? 'external_invitation_accepted_existing_customer'
                : 'external_invitation_accepted_new_customer',
            title: (string) __(
                $providerAlreadyExisted
                    ? 'account.notifications.external_invitation_existing_customer_title'
                    : 'account.notifications.external_invitation_new_customer_title',
                ['company' => $companyName]
            ),
            message: (string) __(
                $providerAlreadyExisted
                    ? 'account.notifications.external_invitation_existing_customer_message'
                    : 'account.notifications.external_invitation_new_customer_message',
                ['company' => $companyName]
            ),
            recipientUserId: $invitation->invited_by !== null ? (int) $invitation->invited_by : null,
            data: [
                'invitation_id' => (int) $invitation->id,
                'provider_account_id' => (int) $providerAccount->id,
                'provider_account_name' => $providerAccount->commercial_name ?: $providerAccount->name,
                'provider_already_existed' => $providerAlreadyExisted,
            ],
        );
    }
}
