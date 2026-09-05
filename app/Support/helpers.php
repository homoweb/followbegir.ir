<?php

/*
|--------------------------------------------------------------------------
| LikeShow global helpers
|--------------------------------------------------------------------------
*/

if (! function_exists('ls_url')) {
    /**
     * Build an absolute URL for one of the application domains.
     *
     * The base URL and the given path are normalized so the helper never
     * emits a trailing slash on the host or a duplicated slash between
     * the host and the path (e.g. "https://panel.x//login").
     */
    function ls_url(string $domainKey, string $path = ''): string
    {
        $base = rtrim((string) config("likeshow.{$domainKey}_url"), '/');
        $path = trim($path, '/');

        return $path === '' ? $base : "{$base}/{$path}";
    }
}

if (! function_exists('ls_main_url')) {
    /**
     * Absolute URL on the main (landing) site.
     */
    function ls_main_url(string $path = ''): string
    {
        return ls_url('main', $path);
    }
}

if (! function_exists('ls_panel_url')) {
    /**
     * Absolute URL on the user panel subdomain.
     */
    function ls_panel_url(string $path = ''): string
    {
        return ls_url('panel', $path);
    }
}

if (! function_exists('ls_admin_url')) {
    /**
     * Absolute URL on the admin panel subdomain.
     */
    function ls_admin_url(string $path = ''): string
    {
        return ls_url('admin', $path);
    }
}
