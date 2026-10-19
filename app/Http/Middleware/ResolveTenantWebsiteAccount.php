<?php

namespace App\Http\Middleware;

use App\Models\Account;
use App\Support\TenantWebsiteHostParser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
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
        $request->attributes->set('operator_portal_account', null);

        $host = strtolower($request->getHost());
        $portalAccount = Account::query()
            ->where('portal_host', $host)
            ->where('status', 'active')
            ->whereHas('accountTypes', function ($query): void {
                $query->where('cat_account_types.code', 'operator')
                    ->where('cat_account_types.active', true);
            })
            ->first();

        if ($portalAccount instanceof Account) {
            $request->attributes->set('operator_portal_account', $portalAccount);
            URL::forceRootUrl($request->getSchemeAndHttpHost());
            URL::forceScheme($request->getScheme());

            return $next($request);
        }

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
