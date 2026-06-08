<?php

namespace App\Support;

use App\Models\Account;
use Illuminate\Http\Request;

/**
 * Asserts the authenticated user's current account has an active business type (provider, operator, …).
 */
final class AccountBusinessTypeGate
{
    public static function resolveCurrentAccount(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        return $account;
    }

    public static function assertHasActiveType(Account $account, string $typeCode): void
    {
        $typeCodes = $account->accountTypes()
            ->where('active', true)
            ->pluck('code');

        abort_unless($typeCodes->contains($typeCode), 403);
    }

    public static function assertProviderAccount(Request $request): Account
    {
        $account = self::resolveCurrentAccount($request);
        self::assertHasActiveType($account, 'provider');

        return $account;
    }

    public static function assertOperatorAccount(Request $request): Account
    {
        $account = self::resolveCurrentAccount($request);
        self::assertHasActiveType($account, 'operator');

        return $account;
    }

    public static function assertAgencyAccount(Request $request): Account
    {
        $account = self::resolveCurrentAccount($request);
        self::assertHasActiveType($account, 'agency');

        return $account;
    }
}
