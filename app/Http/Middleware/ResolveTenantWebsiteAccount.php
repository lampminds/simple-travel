<?php

namespace App\Http\Middleware;

use App\Models\Account;
use App\Support\TenantWebsiteHostParser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the public tenant website account from the Host header and stores it on the request.
 * If the host matches a tenant pattern but no active account exists, responds with 404.
 */
class ResolveTenantWebsiteAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set('tenant_website_account', null);

        $host = $request->getHost();
        $nick = TenantWebsiteHostParser::extractAccountNick($host);

        if ($nick === null) {
            return $next($request);
        }

        $account = Account::query()
            ->whereRaw('LOWER(nick) = ?', [mb_strtolower($nick, 'UTF-8')])
            ->where('status', 'active')
            ->first();

        if ($account === null) {
            abort(404);
        }

        $request->attributes->set('tenant_website_account', $account);

        return $next($request);
    }
}
