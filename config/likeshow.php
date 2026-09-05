<?php

use Illuminate\Support\Env;

/*
|--------------------------------------------------------------------------
| LikeShow Application Configuration
|--------------------------------------------------------------------------
|
| Domain routing and business constants of the platform. Domains are read
| from the environment so local (.test) and production (.ir) environments
| can be switched without touching the code.
|
*/

$scheme = Env::get('LS_SCHEME', 'https');
$mainDomain = Env::get('LS_MAIN_DOMAIN', 'likeshow.test');
$panelDomain = Env::get('LS_PANEL_DOMAIN', 'panel.likeshow.test');
$adminDomain = Env::get('LS_ADMIN_DOMAIN', 'admin.likeshow.test');

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

    'main_url' => Env::get('LS_MAIN_URL', "$scheme://$mainDomain"),
    'panel_url' => Env::get('LS_PANEL_URL', "$scheme://$panelDomain"),
    'admin_url' => Env::get('LS_ADMIN_URL', "$scheme://$adminDomain"),

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */

    'order' => [
        'number_prefix' => 'LS',
        'per_page' => 15,
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */

    'currency' => Env::get('LS_CURRENCY', 'IRT'),
];
