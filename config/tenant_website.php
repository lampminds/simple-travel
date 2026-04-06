<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Production tenant hostname
    |--------------------------------------------------------------------------
    |
    | Public sites use {account_nick}.{domain} (e.g. acme.simple-travel.net).
    |
    */

    'production_domain' => env('TENANT_WEBSITE_DOMAIN', 'simple-travel.net'),

    /*
    |--------------------------------------------------------------------------
    | Development tenant hostname suffix
    |--------------------------------------------------------------------------
    |
    | When APP_URL indicates a .debian dev server, hosts look like:
    | {account_nick}-st.debian
    |
    */

    'development_host_suffix' => env('TENANT_WEBSITE_DEV_SUFFIX', '-st.debian'),

];
