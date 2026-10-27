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
    | When APP_URL indicates a .debian dev server, hosts stay on that vhost:
    | {account_nick}-st.{app host} (for example eb-group-srl-st.simple-travel.debian).
    | A bare {nick}-st.debian name does not reach this project's Apache vhost.
    |
    */

    'development_host_suffix' => env('TENANT_WEBSITE_DEV_SUFFIX', '-st.simple-travel.debian'),

];
