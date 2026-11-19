<?php

namespace Tests\Unit\Support;

use App\Models\Account;
use App\Models\Menu;
use App\Support\TenantWebsiteHostParser;
use App\Support\WebsiteNavigation;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TenantWebsiteHostParserTest extends TestCase
{
    #[Test]
    public function a_local_site_uses_the_development_host(): void
    {
        config([
            'app.url' => 'http://simple-travel.debian',
            'tenant_website.development_host_suffix' => '-st.simple-travel.debian',
            'tenant_website.production_domain' => 'simple-travel.net',
        ]);

        $account = new Account(['nick' => 'EB-Group']);

        $this->assertSame(
            'http://eb-group-st.simple-travel.debian/',
            TenantWebsiteHostParser::publicWebsiteUrlForAccount($account),
        );
    }

    #[Test]
    public function a_production_site_uses_the_nick_subdomain(): void
    {
        config([
            'app.url' => 'https://simple-travel.net',
            'tenant_website.production_domain' => 'simple-travel.net',
        ]);

        $account = new Account(['nick' => 'eb-group']);

        $this->assertSame(
            'https://eb-group.simple-travel.net/',
            TenantWebsiteHostParser::publicWebsiteUrlForAccount($account),
        );
    }

    #[Test]
    public function the_website_menu_opens_the_editor_and_the_public_site_in_a_new_tab(): void
    {
        config([
            'app.url' => 'http://simple-travel.debian',
            'tenant_website.development_host_suffix' => '-st.simple-travel.debian',
        ]);

        $configuration = new Menu([
            'slug' => 'website_configuration',
            'route' => 'website_configuration',
        ]);
        $browse = new Menu([
            'slug' => 'browse_website',
            'route' => 'browse_website',
        ]);

        $this->assertFalse(WebsiteNavigation::opensInNewTab($configuration));
        $this->assertTrue(WebsiteNavigation::opensInNewTab($browse));
        $this->assertSame(route('account.public-profile.edit'), WebsiteNavigation::urlForMenu($configuration));
        $this->assertSame('#', WebsiteNavigation::urlForMenu($browse));
    }
}
