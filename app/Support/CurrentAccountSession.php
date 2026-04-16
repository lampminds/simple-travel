<?php

namespace App\Support;

use App\Http\Middleware\SetPermissionsTeamForRequest;
use App\Models\User;
use Illuminate\Http\Request;

final class CurrentAccountSession
{
    public const SESSION_ACCOUNT_NAME = 'current_account_name';
    public const SESSION_ACCOUNT_TYPE_IDS = 'current_account_type_ids';

    public static function put(Request $request, User $user, int $accountId): void
    {
        $account = $user->accounts()
            ->with(['typeCategories' => fn ($q) => $q->where('cat_account_categories.active', true)])
            ->where('accounts.id', $accountId)
            ->first();

        $typeIds = $account?->typeCategories
            ?->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all() ?? [];

        $request->session()->put(SetPermissionsTeamForRequest::SESSION_CURRENT_ACCOUNT_ID, $accountId);
        $request->session()->put(SetPermissionsTeamForRequest::SESSION_REQUIRES_ACCOUNT_SELECTION, false);
        $request->session()->put(self::SESSION_ACCOUNT_NAME, $account?->commercial_name ?? $account?->name ?? $account?->nick);
        $request->session()->put(self::SESSION_ACCOUNT_TYPE_IDS, $typeIds);
    }

    public static function accountId(Request $request): ?int
    {
        $id = $request->session()->get(SetPermissionsTeamForRequest::SESSION_CURRENT_ACCOUNT_ID);

        return is_numeric($id) ? (int) $id : null;
    }

    /**
     * @return list<int>
     */
    public static function typeIds(Request $request): array
    {
        $raw = $request->session()->get(self::SESSION_ACCOUNT_TYPE_IDS, []);
        if (! is_array($raw)) {
            return [];
        }

        return array_values(array_map('intval', array_filter($raw, fn ($v) => is_numeric($v))));
    }
}

