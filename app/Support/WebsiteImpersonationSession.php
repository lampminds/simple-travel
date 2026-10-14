<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Session flag: user reached the app via a one-time support access link.
 */
final class WebsiteImpersonationSession
{
    public const KEY_ACTIVE_VIA_SUPPORT_TOKEN = 'website_active_via_support_token';

    /**
     * Mark the current session as support impersonation before authentication,
     * so login side effects (such as last_login_at) can skip this request.
     */
    public static function markActive(?Request $request = null): void
    {
        ($request ?? request())->session()->put(self::KEY_ACTIVE_VIA_SUPPORT_TOKEN, true);
    }

    public static function isActive(?Request $request = null): bool
    {
        $request ??= request();

        if (! $request->hasSession()) {
            return false;
        }

        return (bool) $request->session()->get(self::KEY_ACTIVE_VIA_SUPPORT_TOKEN, false);
    }
}
