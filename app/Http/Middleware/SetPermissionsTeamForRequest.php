<?php

namespace App\Http\Middleware;

use App\Support\CurrentAccountSession;
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
    public const SESSION_REQUIRES_ACCOUNT_SELECTION = 'requires_account_selection';

    public function handle(Request $request, Closure $next): Response
    {
        $registrar = app(PermissionRegistrar::class);

        if (! Auth::check()) {
            $registrar->setPermissionsTeamId(null);

            return $next($request);
        }

        $user = Auth::user();
        $platformId = (int) config('permission.platform_account_id', 1);

        $requiresSelection = (bool) $request->session()->get(self::SESSION_REQUIRES_ACCOUNT_SELECTION, false);
        $routeName = (string) optional($request->route())->getName();
        if (
            $requiresSelection
            && ! in_array($routeName, ['account.select', 'account.switch', 'logout'], true)
        ) {
            return redirect()->route('account.select');
        }

        if ($user->belongsToAccount($platformId)) {
            $registrar->setPermissionsTeamId($platformId);

            return $next($request);
        }

        $accountId = $request->session()->get(self::SESSION_CURRENT_ACCOUNT_ID);

        if ($accountId !== null && ! $user->belongsToAccount((int) $accountId)) {
            $accountId = null;
            $request->session()->forget(CurrentAccountSession::SESSION_ACCOUNT_NAME);
            $request->session()->forget(CurrentAccountSession::SESSION_ACCOUNT_TYPE_IDS);
        }

        if ($accountId === null) {
            if ($requiresSelection) {
                $registrar->setPermissionsTeamId(null);

                return $next($request);
            }

            $firstId = $user->accounts()->orderBy('accounts.id')->value('accounts.id');
            $accountId = $firstId !== null ? (int) $firstId : null;
            if ($accountId !== null) {
                CurrentAccountSession::put($request, $user, $accountId);
            }
        }

        $registrar->setPermissionsTeamId($accountId);

        return $next($request);
    }
}
