<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class RoutingController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        // $this->
        // middleware('auth')->
        // except('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()) {
            return redirect('/index');
        } else {
            return redirect('login');
        }
    }

    /**
     * Display a view based on first route param
     *
     * @return \Illuminate\Http\Response
     */
    public function root(Request $request, $first)
    {
        if ($siteView = $this->tenantSiteView($request, (string) $first)) {
            return view($siteView, $this->tenantSiteSharedData($request));
        }

        return view($first);
    }

    /**
     * second level route
     */
    public function secondLevel(Request $request, $first, $second)
    {
        $name = $first.'.'.$second;
        if ($siteView = $this->tenantSiteView($request, $name)) {
            return view($siteView, $this->tenantSiteSharedData($request));
        }

        return view($name);
    }

    /**
     * third level route
     */
    public function thirdLevel(Request $request, $first, $second, $third)
    {
        $name = $first.'.'.$second.'.'.$third;
        if ($siteView = $this->tenantSiteView($request, $name)) {
            return view($siteView, $this->tenantSiteSharedData($request));
        }

        return view($name);
    }

    /**
     * Resolve a tenant public theme view (site::…) when the host is a tenant website and assets are published.
     *
     * @return non-empty-string|null
     */
    private function tenantSiteView(Request $request, string $dotViewName): ?string
    {
        if (! $request->attributes->get('tenant_website_account') instanceof Account) {
            return null;
        }

        if (! site_assets_published()) {
            return null;
        }

        if ($this->isSiteTemplateLayoutOrPartial($dotViewName)) {
            return null;
        }

        $candidate = 'site::'.$dotViewName;

        return View::exists($candidate) ? $candidate : null;
    }

    /**
     * @return array{account: Account, title: string}
     */
    private function tenantSiteSharedData(Request $request): array
    {
        /** @var Account $account */
        $account = $request->attributes->get('tenant_website_account');

        return [
            'account' => $account,
            'title' => $account->commercial_name ?? $account->name ?? $account->nick,
        ];
    }

    private function isSiteTemplateLayoutOrPartial(string $dotViewName): bool
    {
        return str_starts_with($dotViewName, 'layouts.')
            || str_starts_with($dotViewName, 'includes.');
    }
}
