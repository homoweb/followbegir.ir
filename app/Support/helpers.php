<?php

/*
|--------------------------------------------------------------------------
| FollowBegir global helpers
|--------------------------------------------------------------------------
*/

if (! function_exists('fb_url')) {
    /**
     * Build an absolute URL for one of the application domains.
     *
     * The base URL and the given path are normalized so the helper never
     * emits a trailing slash on the host or a duplicated slash between
     * the host and the path (e.g. "https://panel.x//login").
     */
    function fb_url(string $domainKey, string $path = ''): string
    {
        $base = rtrim((string) config("followbegir.{$domainKey}_url"), '/');
        $path = trim($path, '/');

        return $path === '' ? $base : "{$base}/{$path}";
    }
}

if (! function_exists('fb_main_url')) {
    /**
     * Absolute URL on the main (landing) site.
     */
    function fb_main_url(string $path = ''): string
    {
        return fb_url('main', $path);
    }
}

if (! function_exists('fb_panel_url')) {
    /**
     * Absolute URL on the user panel subdomain.
     */
    function fb_panel_url(string $path = ''): string
    {
        return fb_url('panel', $path);
    }
}

if (! function_exists('fb_admin_url')) {
    /**
     * Absolute URL on the admin panel subdomain.
     */
    function fb_admin_url(string $path = ''): string
    {
        return fb_url('admin', $path);
    }
}
