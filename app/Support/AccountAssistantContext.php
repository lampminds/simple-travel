<?php

namespace App\Support;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Resolved locale, account, and business lane for the website assistant.
 */
final class AccountAssistantContext
{
    public function __construct(
        public int $languageId,
        public ?int $accountId,
        public ?int $accountTypeId,
        public ?string $roleTag,
    ) {}

    public static function fromRequest(Request $request, User $user): self
    {
        $account = $user->currentAccount();
        $accountId = $account instanceof Account ? (int) $account->id : null;
        $typeId = $account instanceof Account
            ? AccountDashboardLane::resolvedLaneTypeId($request, $account)
            : null;

        return new self(
            languageId: self::languageIdFromLocale((string) app()->getLocale()),
            accountId: $accountId,
            accountTypeId: $typeId,
            roleTag: self::roleTagForAccountTypeId($typeId),
        );
    }

    public static function languageIdFromLocale(string $locale): int
    {
        $prefix = strtolower(substr($locale, 0, 2));

        return match ($prefix) {
            'en' => 1,
            'pt' => 3,
            default => 2,
        };
    }

    public static function roleTagForAccountTypeId(?int $accountTypeId): ?string
    {
        if ($accountTypeId === null || $accountTypeId < 1) {
            return null;
        }

        $code = AccountType::query()
            ->where('id', $accountTypeId)
            ->value('code');

        return match ((string) $code) {
            'provider' => 'prestador',
            'operator' => 'operador',
            'agency' => 'agencia',
            default => null,
        };
    }
}
