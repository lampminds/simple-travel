<?php

namespace App\Support;

use App\Models\Account;
use App\Models\AccountType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

/**
 * Persists which business "lane" the user chose on account/dashboard (cookie), scoped to current account.
 */
final class AccountDashboardLane
{
    public const COOKIE_NAME = 'acct_dash_lane';

    public const COOKIE_MINUTES = 60 * 24 * 90;

    public static function set(Account $account, int $typeId): void
    {
        $payload = json_encode([
            'account_id' => $account->id,
            'type_id' => $typeId,
        ], JSON_THROW_ON_ERROR);

        Cookie::queue(cookie(
            self::COOKIE_NAME,
            $payload,
            self::COOKIE_MINUTES,
            '/',
            null,
            (bool) config('session.secure'),
            true,
            false,
            config('session.same_site')
        ));
    }

    /**
     * Read validated lane type_id for this account, or null if missing/invalid.
     */
    public static function read(Request $request, Account $account): ?int
    {
        $raw = $request->cookie(self::COOKIE_NAME);
        if (! is_string($raw) || $raw === '') {
            return null;
        }

        try {
            /** @var array{account_id?: int, type_id?: int} $data */
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return null;
        }

        if (($data['account_id'] ?? null) !== $account->id) {
            return null;
        }

        $typeId = isset($data['type_id']) ? (int) $data['type_id'] : 0;
        if ($typeId < 1) {
            return null;
        }

        if (! self::accountHasActiveTypeId($account, $typeId)) {
            return null;
        }

        return $typeId;
    }

    /**
     * Active {@see AccountType} id for this account by canonical lane code (provider|operator|agency).
     */
    public static function activeTypeIdForLaneCode(Account $account, string $laneCode): ?int
    {
        $laneCode = strtolower(trim($laneCode));
        $table = (new AccountType)->getTable();
        $id = $account->accountTypes()
            ->where($table.'.active', true)
            ->where($table.'.code', $laneCode)
            ->value($table.'.id');

        return $id !== null ? (int) $id : null;
    }

    /**
     * Lane for generic routes (catalog, service offers): cookie, else first matching known lane by code.
     *
     * Never relies on fixed numeric ids from {@see AccountTypeCategoryIds}; those may differ per database.
     */
    public static function resolvedLaneTypeId(Request $request, Account $account): ?int
    {
        $fromCookie = self::read($request, $account);
        if ($fromCookie !== null) {
            return $fromCookie;
        }

        foreach (['provider', 'operator', 'agency'] as $code) {
            $id = self::activeTypeIdForLaneCode($account, $code);
            if ($id !== null) {
                return $id;
            }
        }

        return null;
    }

    public static function accountHasActiveTypeId(Account $account, int $typeId): bool
    {
        return $account->accountTypes()
            ->where((new AccountType)->getTable().'.active', true)
            ->where((new AccountType)->getTable().'.id', $typeId)
            ->exists();
    }

    /**
     * Resolve operator type category id for the account when that type is assigned.
     */
    public static function resolveOperatorLaneTypeId(Account $account): ?int
    {
        return self::activeTypeIdForLaneCode($account, 'operator');
    }
}
