<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\GeoIp\IpGeolocator;
use App\Support\WebsiteImpersonationSession;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Stores one lmp_sessions row per successful authentication and resolves the IP once.
 * Support impersonation is not a real login and must not create a row.
 */
class RecordUserLoginSession
{
    public function __construct(private readonly IpGeolocator $geolocator) {}

    public function handle(Login $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        if (WebsiteImpersonationSession::isActive()) {
            return;
        }

        if (! Schema::hasTable('lmp_sessions') || ! Schema::hasTable('lmp_ips')) {
            return;
        }

        try {
            $request = request();
            $ipAddress = $request->ip();
            $lmpIp = filled($ipAddress) ? $this->geolocator->remember((string) $ipAddress) : null;

            $event->user->lmpSessions()->create([
                'lmp_ip_id' => $lmpIp?->getKey(),
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                'user_agent' => $request->userAgent(),
                'logged_in_at' => now(),
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
