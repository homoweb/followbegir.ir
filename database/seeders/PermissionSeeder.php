<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Seed roles and permissions, then map them.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'orders.view', 'orders.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        $admin = Role::query()->firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        Role::query()->firstOrCreate(['name' => 'user']);
    }
}
