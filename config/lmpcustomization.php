<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Filament Panel Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which Filament panel to register the resources with.
    | Set to null to register with all panels.
    |
    */

    'panel_id' => env('LMP_PANEL_ID', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | Resource Configuration
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific resources. Set to false to exclude a resource
    | from being automatically registered.
    |
    */

    'enable_user_resource' => env('LMP_ENABLE_USER_RESOURCE', true),
    'enable_parameter_resource' => env('LMP_ENABLE_PARAMETER_RESOURCE', true),

    /*
    |--------------------------------------------------------------------------
    | Model Customization
    |--------------------------------------------------------------------------
    |
    | Allow customization of the models used by the package. Set to null to use
    | the default package models, or provide your own model classes.
    |
    */

    'user_model' => env('LMP_USER_MODEL', null),
    'parameter_model' => env('LMP_PARAMETER_MODEL', null),

    /*
    |--------------------------------------------------------------------------
    | Resource Customization
    |--------------------------------------------------------------------------
    |
    | Allow customization of resource classes. Set to null to use the default
    | package resources, or provide your own resource classes.
    |
    */

    'user_resource' => env('LMP_USER_RESOURCE', null),
    'parameter_resource' => env('LMP_PARAMETER_RESOURCE', null),

    /*
    |--------------------------------------------------------------------------
    | Navigation Configuration
    |--------------------------------------------------------------------------
    |
    | Configure navigation groups and sorting for the resources.
    |
    */

    'user_navigation_group' => env('LMP_USER_NAVIGATION_GROUP', 'User Management'),
    'parameter_navigation_group' => env('LMP_PARAMETER_NAVIGATION_GROUP', 'Settings'),
    'user_navigation_sort' => env('LMP_USER_NAVIGATION_SORT', 1),
    'parameter_navigation_sort' => env('LMP_PARAMETER_NAVIGATION_SORT', 2),

    /*
    |--------------------------------------------------------------------------
    | Timezone shift
    |--------------------------------------------------------------------------
    |
    | This value complements the timezone setting in the application.
    | Its goal is to convert back and forth between the local time and
    | the UTC time, which is the default timezone for Laravel.
    | Check the helpers `toUtc()` and `fromUtc()` for more details.
    |
    */

    'timezone_shift' => 'America/Argentina/Buenos_Aires',

    /*
    |--------------------------------------------------------------------------
    | Application default date and time formats for display
    |--------------------------------------------------------------------------
    |
    | These are the default date and time formats used throughout the application
    | for displaying dates and times.
    |
    */

    'display_date_format' => 'M d, Y',
    'display_datetime_format' => 'M d, Y H:i',
    'display_time_format' => 'H:i',

    /*
     | --------------------------------------------------------------------------
     | Database date and datetime formats
     | --------------------------------------------------------------------------
     |
     | These are the formats used for storing dates and datetimes in the database.
     |
     */
    'database_date_format' => 'Y-m-d',
    'database_datetime_format' => 'Y-m-d\TH:i:s',

    /*
     * --------------------------------------------------------------------------
     * Decimal point and thousands separator
     * --------------------------------------------------------------------------
     *
     * These settings define the decimal point and thousands separator used in the application.
     *
     */
    'decimal_point' => env('LMP_DECIMAL_POINT', '.'),
    'thousands_separator' => env('LMP_THOUSANDS_SEPARATOR', ','),
    'currency_symbol' => env('LMP_CURRENCY_SYMBOL', '$'),

];
