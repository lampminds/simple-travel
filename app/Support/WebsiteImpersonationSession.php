<?php

namespace App\Support;

/**
 * Session flag: user reached the app via a one-time support access link.
 */
final class WebsiteImpersonationSession
{
    public const KEY_ACTIVE_VIA_SUPPORT_TOKEN = 'website_active_via_support_token';
}
