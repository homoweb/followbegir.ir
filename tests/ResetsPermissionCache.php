<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * RefreshDatabase truncates every table between tests, which wipes
 * spatie/laravel-permission's cached role/permission ids and breaks
 * role re-assignment in later tests (inconsistent results depending on
 * test order). Flushing the package cache after each refresh keeps the
 * suite order-independent.
 */
trait ResetsPermissionCache
{
    protected function setUpTraits(): void
    {
        parent::setUpTraits();

        if (in_array(RefreshDatabase::class, class_uses_recursive(static::class), true)) {
            DB::connection()->flushQueryLog();

            $registrar = app(PermissionRegistrar::class);
            $registrar->forgetCachedPermissions();
            $registrar->clearPermissionsCollection();
        }
    }
}
