<?php

namespace App\Listeners;

use App\Models\User;
use App\Support\WebsiteImpersonationSession;
use Illuminate\Auth\Events\Login;

/**
 * Persists {@see User::$last_login_at} on every successful authentication (web, Filament, OAuth, etc.).
 * Support impersonation is not a real login and must leave the timestamp unchanged.
 */
class RecordUserLastLoginAt
{
    public function handle(Login $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        if (WebsiteImpersonationSession::isActive()) {
            return;
        }

        $event->user->forceFill(['last_login_at' => now()])->saveQuietly();
    }
}
