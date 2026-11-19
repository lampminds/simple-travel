<?php

namespace App\Support;

use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * An agency that signs in on an operator's public website stays on that website.
 * The platform dashboard belongs to the main host and to the operator's own account.
 */
final class OperatorWebsiteAgencyRedirect
{
    public static function applies(Request $request, ?Account $account): bool
    {
        $website = $request->attributes->get('tenant_website_account');
        if (! $website instanceof Account || ! $account instanceof Account) {
            return false;
        }

        if ((int) $account->id === (int) $website->id) {
            return false;
        }

        return $account->hasActiveType('agency');
    }

    /**
     * Public home of the operator website that served this request.
     */
    public static function toLanding(Request $request): RedirectResponse
    {
        $request->session()->forget('url.intended');

        return redirect()->to($request->getSchemeAndHttpHost().'/');
    }
}
