<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets last_login_at once after a successful login (session flag set by login/register handlers).
 */
class RecordLastLogin
{
    public const SESSION_KEY = 'pending_last_login_record';

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && $request->session()->pull(self::SESSION_KEY, false)) {
            Auth::user()->forceFill(['last_login_at' => now()])->saveQuietly();
        }

        return $next($request);
    }
}
