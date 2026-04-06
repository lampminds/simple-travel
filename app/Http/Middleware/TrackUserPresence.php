<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Updates last_seen_at for authenticated users. If kicked_out is true, clears the flag,
 * logs the user out, and redirects to login.
 */
class TrackUserPresence
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = $request->user();
        $user->refresh();

        if ($user->kicked_out) {
            $user->forceFill(['kicked_out' => false])->saveQuietly();

            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('status', __('auth.kicked_out_session'));
        }

        $user->forceFill(['last_seen_at' => now()])->saveQuietly();

        return $next($request);
    }
}
