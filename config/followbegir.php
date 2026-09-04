<?php

use Illuminate\Support\Env;

/*
|--------------------------------------------------------------------------
| FollowBegir Application Configuration
|--------------------------------------------------------------------------
|
| Domain routing and business constants of the platform. Domains are read
| from the environment so local (.test) and production (.ir) environments
| can be switched without touching the code.
|
*/

$scheme = Env::get('FB_SCHEME', 'https');
$mainDomain = Env::get('FB_MAIN_DOMAIN', 'followbegir.test');
$panelDomain = Env::get('FB_PANEL_DOMAIN', 'panel.followbegir.test');
$adminDomain = Env::get('FB_ADMIN_DOMAIN', 'admin.followbegir.test');

return [

    /*
    |--------------------------------------------------------------------------
    | Domains
    |--------------------------------------------------------------------------
    */

    'scheme' => $scheme,

    'main_domain' => $mainDomain,
    'panel_domain' => $panelDomain,
    'admin_domain' => $adminDomain,

    /*
    |--------------------------------------------------------------------------
    | Absolute base URLs (used for cross-domain redirects and callbacks)
    |--------------------------------------------------------------------------
    */

    'main_url' => Env::get('FB_MAIN_URL', "$scheme://$mainDomain"),
    'panel_url' => Env::get('FB_PANEL_URL', "$scheme://$panelDomain"),
    'admin_url' => Env::get('FB_ADMIN_URL', "$scheme://$adminDomain"),

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */

    'order' => [
        'number_prefix' => 'FB',
        'per_page' => 15,
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */

    'currency' => Env::get('FB_CURRENCY', 'IRT'),
];
