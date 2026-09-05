<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    config(['session.domain' => '.likeshow.test']);
});

test('inactive users cannot log in on the panel', function () {
    $user = User::factory()->inactive()->create();

    $this->post('https://panel.likeshow.test/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertSessionHasErrors(['email']);

    $this->assertGuest();
});

test('deactivated users are logged out on their next request', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $user->forceFill(['is_active' => false])->save();

    $response = $this->get('https://panel.likeshow.test/orders');

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $this->assertGuest();
});

test('panel users see only their own orders', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();

    $followersProduct = Product::factory()->followers()->create();
    $likesProduct = Product::factory()->likes()->create();

    Order::factory()->for($owner)->for($followersProduct)->count(2)->create();
    Order::factory()->for($intruder)->for($likesProduct)->create();

    $this->actingAs($owner)
        ->get('https://panel.likeshow.test/orders')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Panel/Orders/Index')
            ->has('orders.data', 2));

    $this->actingAs($intruder)
        ->get('https://panel.likeshow.test/orders/'.Order::query()->where('user_id', $owner->id)->firstOrFail()->id)
        ->assertNotFound();

    $own = Order::query()->where('user_id', $intruder->id)->firstOrFail();
    $this->actingAs($intruder)
        ->get('https://panel.likeshow.test/orders/'.$own->id)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Panel/Orders/Show')
            ->where('order.order_number', $own->order_number));
});

test('registration validates password confirmation', function () {
    $this->seed(PermissionSeeder::class);

    $this->post('https://panel.likeshow.test/register', [
        'name' => 'کاربر مهمان',
        'email' => 'guest@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different123',
    ])->assertSessionHasErrors(['password']);

    expect(User::query()->where('email', 'guest@example.com')->exists())->toBeFalse();
});
