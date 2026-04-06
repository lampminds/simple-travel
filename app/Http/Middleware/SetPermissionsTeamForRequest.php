<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

/**
 * Spatie Permission "teams" use account_id as team. Runs after the session is available.
 * Session {@see SESSION_CURRENT_ACCOUNT_ID} is set on login/register/switch; otherwise
 * the first account from the account_user pivot is used.
 *
 * Users linked to the reserved platform account (config permission.platform_account_id, default 1)
 * always resolve roles/permissions against that team so global (cross-tenant) roles apply regardless
 * of which account is selected for data scope.
 */
class SetPermissionsTeamForRequest
{
    public const SESSION_CURRENT_ACCOUNT_ID = 'current_account_id';

    public function handle(Request $request, Closure $next): Response
    {
        $registrar = app(PermissionRegistrar::class);

        if (! Auth::check()) {
            $registrar->setPermissionsTeamId(null);

            return $next($request);
        }

        $user = Auth::user();
        $platformId = (int) config('permission.platform_account_id', 1);

        if ($user->belongsToAccount($platformId)) {
            $registrar->setPermissionsTeamId($platformId);

            return $next($request);
        }

        $accountId = $request->session()->get(self::SESSION_CURRENT_ACCOUNT_ID);

        if ($accountId !== null && ! $user->belongsToAccount((int) $accountId)) {
            $accountId = null;
        }

        if ($accountId === null) {
            $firstId = $user->accounts()->orderBy('accounts.id')->value('accounts.id');
            $accountId = $firstId !== null ? (int) $firstId : null;
            if ($accountId !== null) {
                $request->session()->put(self::SESSION_CURRENT_ACCOUNT_ID, $accountId);
            }
        }

        $registrar->setPermissionsTeamId($accountId);

        return $next($request);
    }
}
