<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Controllers\ParameterReaderController;
use App\Models\Account;
use App\Models\ProviderSupplyRelationship;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * A provider invites another provider (who may also be an operator) to supply combo components.
 */
final class ProviderSupplyInvitationService
{
    public const OUTCOME_SENT = 'sent';

    public const OUTCOME_CHOOSE = 'choose';

    public function __construct(
        private readonly ExternalCompanyInvitationService $externalCompanyInvitation,
        private readonly ParameterReaderController $parameterReader,
    ) {
    }

    /**
     * @return array{outcome: string, choices?: list<array{id: int, label: string}>}
     */
    public function send(
        int $buyerAccountId,
        int $invitedByUserId,
        string $name,
        string $email,
        ?string $companyName,
        ?int $selectedAccountId,
    ): array {
        UserInvitation::syncExpiredForAccount($buyerAccountId);

        $normalizedEmail = Str::lower(trim($email));
        $existingUser = $this->externalCompanyInvitation->findActiveUserByEmail($normalizedEmail);
        if ($existingUser === null) {
            throw ValidationException::withMessages([
                'email' => __('account.provider_supply.invite_must_exist'),
            ]);
        }

        $providerAccounts = $this->providerAccountsExcept($existingUser, $buyerAccountId);

        if ($selectedAccountId !== null && $selectedAccountId > 0) {
            $target = $providerAccounts->firstWhere('id', $selectedAccountId);
            if ($target === null) {
                throw ValidationException::withMessages([
                    'invited_account_id' => __('account.provider_supply.invite_not_provider'),
                ]);
            }
        } elseif ($providerAccounts->isEmpty()) {
            throw ValidationException::withMessages([
                'email' => __('account.provider_supply.invite_not_provider'),
            ]);
        } elseif ($providerAccounts->count() > 1) {
            return [
                'outcome' => self::OUTCOME_CHOOSE,
                'choices' => $this->externalCompanyInvitation->accountChoicesForForm($providerAccounts),
            ];
        } else {
            $target = $providerAccounts->first();
        }

        $targetId = (int) $target->id;

        $duplicatePending = UserInvitation::query()
            ->where('account_id', $buyerAccountId)
            ->where('purpose', UserInvitation::PURPOSE_PROVIDER_SUPPLY)
            ->where('status', UserInvitation::STATUS_PENDING)
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->where(function ($query) use ($normalizedEmail, $targetId): void {
                $query->where('email', $normalizedEmail)
                    ->orWhere('invited_account_id', $targetId);
            })
            ->exists();

        if ($duplicatePending) {
            throw ValidationException::withMessages([
                'email' => __('invitations.duplicate_pending'),
            ]);
        }

        $alreadyLinked = ProviderSupplyRelationship::query()
            ->where('buyer_account_id', $buyerAccountId)
            ->where('supplier_account_id', $targetId)
            ->where('status', ProviderSupplyRelationship::STATUS_APPROVED)
            ->exists();

        if ($alreadyLinked) {
            throw ValidationException::withMessages([
                'email' => __('account.provider_supply.already_linked'),
            ]);
        }

        $invitation = $this->externalCompanyInvitation->createPendingForExistingUser(
            operatorAccountId: $buyerAccountId,
            invitedByUserId: $invitedByUserId,
            name: Str::title(trim($name)),
            email: $normalizedEmail,
            roleId: Role::platformTemplateRoleIdOrFail('owner'),
            expirationDays: $this->parameterReader->invitationExpirationDays($buyerAccountId),
            invitedUserId: (int) $existingUser->id,
            invitedAccountId: $targetId,
            companyName: trim((string) $companyName) !== '' ? trim((string) $companyName) : null,
            purpose: UserInvitation::PURPOSE_PROVIDER_SUPPLY,
        );

        $this->externalCompanyInvitation->deliverInvitation($invitation);

        return ['outcome' => self::OUTCOME_SENT];
    }

    /**
     * Provider accounts of the invitee, including accounts that are also operators.
     *
     * @return Collection<int, Account>
     */
    private function providerAccountsExcept(User $user, int $exceptAccountId): Collection
    {
        return $user->accounts()
            ->where('accounts.id', '!=', $exceptAccountId)
            ->whereHas('accountTypes', function ($query): void {
                $query->where('cat_account_types.code', 'provider')
                    ->where('cat_account_types.active', true);
            })
            ->orderBy('accounts.id')
            ->get();
    }
}
