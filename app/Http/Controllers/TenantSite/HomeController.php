<?php

namespace App\Http\Controllers\TenantSite;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * SaaS landing on the main host; minimal welcome on tenant website hosts.
     */
    public function __invoke(Request $request): View
    {
        $account = $request->attributes->get('tenant_website_account');

        if ($account instanceof Account) {
            $data = [
                'account' => $account,
                'title' => $account->commercial_name ?? $account->name ?? $account->nick,
            ];

            if (site_assets_published()) {
                return view('tenant.site.home', $data);
            }

            return view('tenant.welcome', $data);
        }

        return view('landings.saas');
    }
}
