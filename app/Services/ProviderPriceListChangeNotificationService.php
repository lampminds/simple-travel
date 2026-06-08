<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\PriceList;
use App\Models\User;
use App\Notifications\ProviderPriceListUpdatedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

/**
 * Notifies operator accounts when a provider price list with active assignments is updated.
 */
final class ProviderPriceListChangeNotificationService
{
    public function __construct(
        private readonly ProviderPriceListActiveAssignmentService $activeAssignments,
        private readonly AccountNotificationService $accountNotifications,
        private readonly AccountRelationshipsListingService $accountLabels,
    ) {
    }

    public function notifyIfRequested(
        PriceList $priceList,
        Account $providerAccount,
        User $actingUser,
        string $notificationChoice,
        ?string $customMessage,
        bool $sendEmail,
        bool $ccActingUser,
    ): void {
        if ($notificationChoice !== 'notify') {
            return;
        }

        if (! $this->activeAssignments->hasActiveOperatorAssignments($priceList)) {
            return;
        }

        $providerLabel = $this->accountLabels->accountDisplayName($providerAccount);
        $messageText = $this->buildInAppMessage($priceList, $providerLabel, $customMessage);
        $ccEmail = $ccActingUser ? trim((string) $actingUser->email) : null;

        foreach ($this->activeAssignments->activeAssignments($priceList) as $assignment) {
            $operatorAccountId = (int) $assignment->operator_id;
            if ($operatorAccountId <= 0) {
                continue;
            }

            $this->accountNotifications->createForAccount(
                accountId: $operatorAccountId,
                type: 'provider_price_list_updated',
                title: (string) __('account.price_lists.notification_in_app.title', [
                    'provider' => $providerLabel,
                    'list' => $priceList->name,
                ]),
                message: $messageText,
                recipientUserId: null,
                data: [
                    'provider_account_id' => (int) $providerAccount->id,
                    'provider_account_name' => $providerLabel,
                    'price_list_id' => (int) $priceList->id,
                    'price_list_name' => $priceList->name,
                    'updated_by_user_id' => (int) $actingUser->id,
                    'updated_by_user_name' => $actingUser->name,
                    'custom_message' => trim((string) ($customMessage ?? '')) ?: null,
                ],
            );

            if ($sendEmail) {
                $this->sendEmailToOperatorOwners(
                    operatorAccountId: $operatorAccountId,
                    priceList: $priceList,
                    providerAccount: $providerAccount,
                    customMessage: $customMessage,
                    ccEmail: $ccEmail,
                );
            }
        }
    }

    private function buildInAppMessage(PriceList $priceList, string $providerLabel, ?string $customMessage): string
    {
        $base = (string) __('account.price_lists.notification_in_app.message', [
            'provider' => $providerLabel,
            'list' => $priceList->name,
        ]);

        $customMessage = trim((string) ($customMessage ?? ''));
        if ($customMessage === '') {
            return $base;
        }

        return $base."\n\n".$customMessage;
    }

    private function sendEmailToOperatorOwners(
        int $operatorAccountId,
        PriceList $priceList,
        Account $providerAccount,
        ?string $customMessage,
        ?string $ccEmail,
    ): void {
        $owners = $this->ownerUsersForAccount($operatorAccountId);
        if ($owners->isEmpty()) {
            return;
        }

        Notification::send(
            $owners,
            new ProviderPriceListUpdatedNotification(
                priceList: $priceList,
                providerAccount: $providerAccount,
                customMessage: $customMessage,
                ccEmail: $ccEmail,
            ),
        );
    }

    /**
     * @return Collection<int, User>
     */
    private function ownerUsersForAccount(int $accountId): Collection
    {
        return User::query()
            ->whereHas('accounts', fn ($query) => $query->where('accounts.id', $accountId))
            ->where('activation_state', User::ACTIVATION_ACTIVE)
            ->orderBy('name')
            ->get()
            ->filter(fn (User $user): bool => $user->hasRoleForAccountId('owner', $accountId))
            ->values();
    }
}
