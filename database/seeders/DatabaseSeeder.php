<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database: roles, the admin account,
     * the default Instagram products with tiered pricing.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);

        $admin = User::query()->create([
            'name' => (string) env('ADMIN_NAME', 'مدیر سیستم'),
            'email' => (string) env('ADMIN_EMAIL', 'admin@likeshow.ir'),
            'password' => env('ADMIN_PASSWORD', 'password'),
            'is_active' => true,
        ]);

        $admin->assignRole('admin');

        $this->call(ProductSeeder::class);
    }
}
