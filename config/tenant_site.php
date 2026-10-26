<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Blade views for the public tenant website (subdomain)
    |--------------------------------------------------------------------------
    |
    | Separate from resources/views, which is the Simple Travel marketing site
    | and the account area. Referenced in Blade as the "site" namespace
    | (site::layouts.main, site::includes.navbar, …).
    |
    */

    'views_path' => 'tenant-site/views',

    /*
    |--------------------------------------------------------------------------
    | Public URL prefix for tenant theme static files (under public/)
    |--------------------------------------------------------------------------
    |
    | Already published under public/. To refresh them from the original
    | theme checkout (resources/site, not in git): php artisan site:publish-assets
    |
    */

    'assets_path' => env('TENANT_SITE_ASSETS_PATH', 'site/assets'),

];
