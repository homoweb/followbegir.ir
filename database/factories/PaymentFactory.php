<?php

namespace Database\Factories;

use App\Enums\PaymentTxnStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'user_id' => fn (array $attributes) => Order::query()
                ->find($attributes['order_id'])?->user_id,
            'amount' => fn (array $attributes) => max(1000, (int) Order::query()
                ->find($attributes['order_id'])?->total_price),
            'gateway' => 'local',
            'authority' => 'SB'.fake()->numerify('##########'),
            'reference_id' => null,
            'card_number' => null,
            'status' => PaymentTxnStatus::Pending,
            'gateway_response' => null,
            'paid_at' => null,
        ];
    }

    /**
     * A verified, successful payment.
     */
    public function success(): static
    {
        return $this->state(fn () => [
            'status' => PaymentTxnStatus::Success,
            'reference_id' => (string) fake()->numberBetween(1000000000, 9999999999),
            'card_number' => '****'.fake()->numerify('####'),
            'paid_at' => Carbon::now(),
        ]);
    }

    /**
     * A failed payment attempt.
     */
    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => PaymentTxnStatus::Failed,
        ]);
    }
}
