<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\PriceList;
use App\Models\ProviderSupplyPriceListAssignment;
use App\Models\User;
use App\Notifications\ProviderPriceListUpdatedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Carbon;

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

        $operatorAssignments = $this->activeAssignments->activeAssignments($priceList);
        $buyerAssignments = $this->activeBuyerAssignments($priceList);
        if ($operatorAssignments->isEmpty() && $buyerAssignments->isEmpty()) {
            return;
        }

        $providerLabel = $this->accountLabels->accountDisplayName($providerAccount);
        $messageText = $this->buildInAppMessage($priceList, $providerLabel, $customMessage);
        $ccEmail = $ccActingUser ? trim((string) $actingUser->email) : null;

        foreach ($operatorAssignments as $assignment) {
            $operatorAccountId = (int) $assignment->operator_id;
            if ($operatorAccountId <= 0) {
                continue;
            }

            $this->notifyAccount(
                accountId: $operatorAccountId,
                priceList: $priceList,
                providerAccount: $providerAccount,
                providerLabel: $providerLabel,
                messageText: $messageText,
                actingUser: $actingUser,
                customMessage: $customMessage,
                sendEmail: $sendEmail,
                ccEmail: $ccEmail,
            );
        }

        foreach ($buyerAssignments as $assignment) {
            $buyerAccountId = (int) $assignment->buyer_account_id;
            if ($buyerAccountId <= 0) {
                continue;
            }

            $this->notifyAccount(
                accountId: $buyerAccountId,
                priceList: $priceList,
                providerAccount: $providerAccount,
                providerLabel: $providerLabel,
                messageText: $messageText,
                actingUser: $actingUser,
                customMessage: $customMessage,
                sendEmail: $sendEmail,
                ccEmail: $ccEmail,
            );
        }
    }

    private function notifyAccount(
        int $accountId,
        PriceList $priceList,
        Account $providerAccount,
        string $providerLabel,
        string $messageText,
        User $actingUser,
        ?string $customMessage,
        bool $sendEmail,
        ?string $ccEmail,
    ): void {
        $this->accountNotifications->createForAccount(
            accountId: $accountId,
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
            $this->sendEmailToAccountOwners(
                accountId: $accountId,
                priceList: $priceList,
                providerAccount: $providerAccount,
                customMessage: $customMessage,
                ccEmail: $ccEmail,
            );
        }
    }

    /**
     * @return Collection<int, ProviderSupplyPriceListAssignment>
     */
    private function activeBuyerAssignments(PriceList $priceList): Collection
    {
        if (! $priceList->is_active) {
            return collect();
        }

        $today = Carbon::today();

        return ProviderSupplyPriceListAssignment::query()
            ->where('provider_price_list_id', $priceList->id)
            ->where('is_active', true)
            ->orderBy('id')
            ->get()
            ->filter(function (ProviderSupplyPriceListAssignment $assignment) use ($today): bool {
                if ($assignment->valid_from && $today->toDateString() < Carbon::parse($assignment->valid_from)->toDateString()) {
                    return false;
                }

                if ($assignment->valid_to && $today->toDateString() > Carbon::parse($assignment->valid_to)->toDateString()) {
                    return false;
                }

                return true;
            })
            ->values();
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

    private function sendEmailToAccountOwners(
        int $accountId,
        PriceList $priceList,
        Account $providerAccount,
        ?string $customMessage,
        ?string $ccEmail,
    ): void {
        $owners = $this->ownerUsersForAccount($accountId);
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
