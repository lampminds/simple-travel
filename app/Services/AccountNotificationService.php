<?php

namespace App\Services;

use App\Models\AccountNotification;
use App\Models\User;

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
}
