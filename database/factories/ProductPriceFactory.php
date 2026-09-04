<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductPrice>
 *
 * @method ProductPrice create(array $attributes = [])
 */
class ProductPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'min_quantity' => 1000,
            'max_quantity' => 5000,
            'price' => 120,
        ];
    }
}
