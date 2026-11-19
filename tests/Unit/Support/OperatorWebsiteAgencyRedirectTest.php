<?php

namespace Tests\Unit\Support;

use App\Models\Account;
use App\Models\AccountType;
use App\Support\OperatorWebsiteAgencyRedirect;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OperatorWebsiteAgencyRedirectTest extends TestCase
{
    #[Test]
    public function an_agency_on_an_operator_website_stays_on_that_site(): void
    {
        $website = $this->account(10);
        $agency = $this->account(22, 'agency');

        $this->assertTrue(OperatorWebsiteAgencyRedirect::applies(
            $this->requestFor($website),
            $agency,
        ));
    }

    #[Test]
    public function the_operator_account_itself_still_enters_the_platform(): void
    {
        $website = $this->account(10, 'operator');

        $this->assertFalse(OperatorWebsiteAgencyRedirect::applies(
            $this->requestFor($website),
            $website,
        ));
    }

    #[Test]
    public function a_provider_on_the_operator_website_is_not_treated_as_an_agency(): void
    {
        $website = $this->account(10);
        $provider = $this->account(30, 'provider');

        $this->assertFalse(OperatorWebsiteAgencyRedirect::applies(
            $this->requestFor($website),
            $provider,
        ));
    }

    #[Test]
    public function the_main_host_keeps_the_platform_dashboard(): void
    {
        $agency = $this->account(22, 'agency');
        $request = Request::create('http://simple-travel.debian/login', 'POST');

        $this->assertFalse(OperatorWebsiteAgencyRedirect::applies($request, $agency));
    }

    #[Test]
    public function the_landing_redirect_uses_the_operator_host_and_drops_the_dashboard_target(): void
    {
        $request = Request::create('http://eb-group-srl-st.simple-travel.debian/login', 'POST');
        $request->setLaravelSession($this->app['session.store']);
        $request->session()->put('url.intended', 'http://eb-group-srl-st.simple-travel.debian/account/dashboard');

        $response = OperatorWebsiteAgencyRedirect::toLanding($request);

        $this->assertSame('http://eb-group-srl-st.simple-travel.debian/', $response->getTargetUrl());
        $this->assertFalse($request->session()->has('url.intended'));
    }

    private function requestFor(Account $website): Request
    {
        $request = Request::create('http://eb-group-srl-st.simple-travel.debian/login', 'POST');
        $request->attributes->set('tenant_website_account', $website);

        return $request;
    }

    private function account(int $id, ?string $typeCode = null): Account
    {
        $account = new Account;
        $account->id = $id;

        $types = collect();
        if ($typeCode !== null) {
            $type = new AccountType;
            $type->code = $typeCode;
            $type->active = true;
            $types->push($type);
        }

        $account->setRelation('accountTypes', $types);

        return $account;
    }
}
