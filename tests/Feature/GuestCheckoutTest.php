<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    // Allow the session cookie to flow between the three subdomains and
    // pin the configured main URL scheme for deterministic assertions.
    config(['session.domain' => '.followbegir.test']);
    config(['followbegir.main_url' => 'https://followbegir.test']);
});

test('guest checkout stores a draft and redirects to the panel login', function () {
    $product = Product::factory()->followers()->withTiers()->create();

    $response = $this->post('https://followbegir.test/checkout/'.$product->id, [
        'quantity' => 5000,
        'target_username' => 'my_page',
    ]);

    $response->assertRedirect('https://panel.followbegir.test/login');
    $response->assertSessionHas('checkout_draft');
    $response->assertSessionHas('info');

    expect(Order::query()->count())->toBe(0);
});

test('guest checkout answers inertia requests with x-inertia-location instead of a cross-origin 302', function () {
    $product = Product::factory()->followers()->withTiers()->create();

    // The frontend posts the checkout form via Inertia (XHR). A 302 to the
    // panel domain would be blocked by the browser as CORS, so the response
    // must instead carry 409 + X-Inertia-Location (a full client-side
    // page visit to the panel login).
    $response = $this->post('https://followbegir.test/checkout/'.$product->id, [
        'quantity' => 5000,
        'target_username' => 'my_page',
    ], ['X-Inertia' => 'true']);

    $response->assertStatus(409);
    expect($response->headers->get('X-Inertia-Location'))
        ->toBe('https://panel.followbegir.test/login');

    $response->assertSessionHas('checkout_draft');
    $response->assertSessionHas('info');

    expect(Order::query()->count())->toBe(0);
});

test('guest checkout resumes after registering on the panel', function () {
    $this->seed(PermissionSeeder::class);

    $product = Product::factory()->followers()->withTiers()->create();

    $this->post('https://followbegir.test/checkout/'.$product->id, [
        'quantity' => 5000,
        'target_username' => 'my_page',
    ]);

    $response = $this->post('https://panel.followbegir.test/register', [
        'name' => 'علی رضایی',
        'email' => 'ali@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('https://followbegir.test/order/resume');

    $resume = $this->get('https://followbegir.test/order/resume');

    $resume->assertRedirect();
    expect($resume->headers->get('Location'))->toContain('/payment/review/');
    $resume->assertSessionMissing('checkout_draft');

    $order = Order::query()->sole();
    $user = User::query()->where('email', 'ali@example.com')->firstOrFail();

    expect($order->user_id)->toBe($user->getKey())
        ->and($user->hasRole('user'))->toBeTrue()
        ->and($order->quantity)->toBe(5000)
        ->and($order->target_username)->toBe('my_page')
        // 5000 units fall into the [1000, 5000] tier priced at 120 per 1000.
        ->and($order->unit_price)->toBe(120)
        ->and($order->total_price)->toBe(600)
        ->and($order->status->value)->toBe('pending')
        ->and($order->payment_status->value)->toBe('unpaid');
});

test('login resumes a draft for inertia requests via x-inertia-location instead of a cross-origin 302', function () {
    $user = User::factory()->create();
    $product = Product::factory()->followers()->withTiers()->create();

    $this->post('https://followbegir.test/checkout/'.$product->id, [
        'quantity' => 20000,
        'target_username' => 'another_page',
    ]);

    // The panel login form posts via Inertia (XHR). Redirecting that XHR to
    // the main domain would be blocked as CORS; the client must perform a
    // full page visit via 409 + X-Inertia-Location instead.
    $response = $this->post('https://panel.followbegir.test/login', [
        'email' => $user->email,
        'password' => 'password',
    ], ['X-Inertia' => 'true']);

    $response->assertStatus(409);
    expect($response->headers->get('X-Inertia-Location'))
        ->toBe('https://followbegir.test/order/resume');
});

test('login ignores cross-origin intended urls to keep the response same-origin', function () {
    $user = User::factory()->create();

    // Simulate a stale intended URL left behind by an earlier guest redirect
    // from the main site; following it would make the XHR response a blocked
    // cross-origin redirect.
    session(['url.intended' => 'https://followbegir.test/checkout/1']);

    $response = $this->post('https://panel.followbegir.test/login', [
        'email' => $user->email,
        'password' => 'password',
    ], ['X-Inertia' => 'true']);

    $response->assertRedirect('https://panel.followbegir.test/orders');
});

test('guest checkout resumes after logging in on the panel', function () {
    $user = User::factory()->create();
    $product = Product::factory()->followers()->withTiers()->create();

    $this->post('https://followbegir.test/checkout/'.$product->id, [
        'quantity' => 20000,
        'target_username' => 'another_page',
    ]);

    $response = $this->post('https://panel.followbegir.test/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect('https://followbegir.test/order/resume');

    $this->get('https://followbegir.test/order/resume');

    $order = Order::query()->sole();

    expect($order->user_id)->toBe($user->getKey())
        // 20000 units fall into the [5001, 20000] tier priced at 100 per 1000.
        ->and($order->unit_price)->toBe(100)
        ->and($order->total_price)->toBe(2000);
});

test('checkout rejects invalid usernames and quantities', function () {
    $product = Product::factory()->followers()->withTiers()->create();

    $this->post('https://followbegir.test/checkout/'.$product->id, [
        'quantity' => 5000,
        'target_username' => 'نامشخص!',
    ])->assertSessionHasErrors(['target_username']);

    $this->post('https://followbegir.test/checkout/'.$product->id, [
        'quantity' => 500,
        'target_username' => 'my_page',
    ])->assertSessionHasErrors(['quantity']);

    expect(Order::query()->count())->toBe(0);
});

test('the checkout page renders for active products only', function () {
    $product = Product::factory()->followers()->withTiers()->create();
    $inactive = Product::factory()->likes()->inactive()->create();

    $this->get('https://followbegir.test/checkout/'.$product->id)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Main/Checkout')
            ->where('product.id', $product->id)
            ->count('product.prices', 3));

    $this->get('https://followbegir.test/checkout/'.$inactive->id)
        ->assertNotFound();
});
