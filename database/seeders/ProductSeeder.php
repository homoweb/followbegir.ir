<?php

namespace Database\Seeders;

use App\Enums\Platform;
use App\Enums\ProductType;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * The default Instagram catalog with tiered pricing.
     */
    public function run(): void
    {
        $products = [
            [
                'platform' => Platform::Instagram,
                'type' => ProductType::Followers,
                'title' => 'فالوور اینستاگرام',
                'description' => 'افزایش فالوور واقعی و باکیفیت پیج اینستاگرام شما',
                'min_quantity' => 1000,
                'max_quantity' => 100000,
                'step_quantity' => 1000,
                'base_price' => 90000,
                'sort_order' => 1,
                'tiers' => [
                    ['min_quantity' => 10000, 'max_quantity' => 49999, 'price' => 80000],
                    ['min_quantity' => 50000, 'max_quantity' => 100000, 'price' => 70000],
                ],
            ],
            [
                'platform' => Platform::Instagram,
                'type' => ProductType::Likes,
                'title' => 'لایک اینستاگرام',
                'description' => 'افزایش لایک پست‌های پیج اینستاگرام شما',
                'min_quantity' => 1000,
                'max_quantity' => 50000,
                'step_quantity' => 1000,
                'base_price' => 45000,
                'sort_order' => 2,
                'tiers' => [
                    ['min_quantity' => 20000, 'max_quantity' => 50000, 'price' => 38000],
                ],
            ],
        ];

        foreach ($products as $data) {
            $tiers = $data['tiers'];

            unset($data['tiers']);

            $product = Product::query()->updateOrCreate([
                'platform' => $data['platform'],
                'type' => $data['type'],
            ], [
                ...$data,
                'is_active' => true,
            ]);

            foreach ($tiers as $tier) {
                $product->prices()->updateOrCreate([
                    'min_quantity' => $tier['min_quantity'],
                ], $tier);
            }
        }
    }
}
