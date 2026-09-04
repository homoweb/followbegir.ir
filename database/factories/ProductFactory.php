<?php

namespace Database\Factories;

use App\Enums\Platform;
use App\Enums\ProductType;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(ProductType::cases());

        return [
            'type' => $type,
            'platform' => Platform::Instagram,
            'title' => $type->label().' اینستاگرام',
            'description' => fake()->sentence(),
            'min_quantity' => 1000,
            'max_quantity' => 1000000,
            'step_quantity' => 1000,
            'base_price' => fake()->numberBetween(80, 200),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    /**
     * Followers product (used by the seeder and happy-path tests).
     */
    public function followers(): static
    {
        return $this->state(fn () => [
            'type' => ProductType::Followers,
            'platform' => Platform::Instagram,
            'title' => 'فالوور اینستاگرام',
        ]);
    }

    /**
     * Likes product.
     */
    public function likes(): static
    {
        return $this->state(fn () => [
            'type' => ProductType::Likes,
            'platform' => Platform::Instagram,
            'title' => 'لایک اینستاگرام',
        ]);
    }

    /**
     * Indicate that the product is hidden from the storefront.
     */
    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }

    /**
     * Attach a simple two-tier price ladder to the product.
     */
    public function withTiers(): static
    {
        return $this->afterCreating(function (Product $product): void {
            $product->prices()->createMany([
                ['min_quantity' => 1000, 'max_quantity' => 5000, 'price' => 120],
                ['min_quantity' => 5001, 'max_quantity' => 20000, 'price' => 100],
                ['min_quantity' => 20001, 'max_quantity' => 1000000, 'price' => 90],
            ]);
        });
    }
}
