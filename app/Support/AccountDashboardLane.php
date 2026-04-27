<?php

namespace App\Support;

use App\Models\Account;
use App\Models\AccountCategory;
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
     * Lane for generic routes (catalog, relationships): cookie, else single-type account default.
     */
    public static function resolvedLaneTypeId(Request $request, Account $account): ?int
    {
        $fromCookie = self::read($request, $account);
        if ($fromCookie !== null) {
            return $fromCookie;
        }

        $active = $account->typeCategories()
            ->where((new AccountCategory)->getTable().'.active', true)
            ->pluck((new AccountCategory)->getTable().'.id')
            ->map(fn ($id) => (int) $id)
            ->values();

        if ($active->count() === 1) {
            return (int) $active->first();
        }

        return null;
    }

    public static function accountHasActiveTypeId(Account $account, int $typeId): bool
    {
        return $account->typeCategories()
            ->where((new AccountCategory)->getTable().'.active', true)
            ->where((new AccountCategory)->getTable().'.id', $typeId)
            ->exists();
    }

    /**
     * Resolve operator type category id for the account when that type is assigned.
     */
    public static function resolveOperatorLaneTypeId(Account $account): ?int
    {
        if ($account->typeCategories()
            ->where((new AccountCategory)->getTable().'.active', true)
            ->where((new AccountCategory)->getTable().'.id', AccountTypeCategoryIds::OPERATOR)
            ->exists()) {
            return AccountTypeCategoryIds::OPERATOR;
        }

        return null;
    }
}
