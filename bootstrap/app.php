<?php

use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'active' => EnsureUserIsActive::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        // Send "guest" and "auth" redirects to the right subdomain. Every
        // redirect must stay same-origin with the requesting host: a
        // cross-origin 302 on an Inertia (XHR) request is blocked by the
        // browser as CORS. Cross-domain hops happen only through full-page
        // navigations (links or Inertia::location responses).
        $middleware->redirectGuestsTo(function (Request $request) {
            $host = (string) $request->getHost();

            if (str_starts_with($host, 'admin.')) {
                return route('admin.login');
            }

            if (str_starts_with($host, 'panel.')) {
                return route('panel.login');
            }

            // The main site has no login page of its own; keep its guest
            // redirects same-origin instead of bouncing XHRs to the panel.
            return route('main.home');
        });

        $middleware->redirectUsersTo(
            fn (Request $request) => str_starts_with((string) $request->getHost(), 'admin.')
                ? route('admin.users.index')
                : route('panel.orders.index'),
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
