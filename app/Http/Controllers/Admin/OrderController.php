<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    /**
     * All orders with optional filters.
     */
    public function index(Request $request): Response
    {
        $orders = Order::query()
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where(
                'status',
                $status,
            ))
            ->when($request->string('payment_status')->toString(), fn ($query, $paymentStatus) => $query->where(
                'payment_status',
                $paymentStatus,
            ))
            ->when($request->string('q')->toString(), function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('order_number', 'like', "%{$search}%")
                        ->orWhere('target_username', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->with('user:id,name,email')
            ->paginate(config('followbegir.order.per_page'))
            ->through(fn (Order $order): array => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'product_title' => $order->product_title,
                'target_username' => $order->target_username,
                'quantity' => $order->quantity,
                'total_price' => $order->total_price,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'user' => $order->user ? [
                    'id' => $order->user->id,
                    'name' => $order->user->name,
                    'email' => $order->user->email,
                ] : null,
                'created_at' => $order->created_at->toIso8601String(),
            ]);

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => [
                'status' => $request->string('status')->toString(),
                'payment_status' => $request->string('payment_status')->toString(),
                'q' => $request->string('q')->toString(),
            ],
        ]);
    }

    /**
     * A single order detail with its payments.
     */
    public function show(Order $order): Response
    {
        $order->load(['user:id,name,email', 'payments']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'product_title' => $order->product_title,
                'target_username' => $order->target_username,
                'quantity' => $order->quantity,
                'unit_price' => $order->unit_price,
                'total_price' => $order->total_price,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'paid_at' => $order->paid_at?->toIso8601String(),
                'created_at' => $order->created_at->toIso8601String(),
                'user' => $order->user ? [
                    'id' => $order->user->id,
                    'name' => $order->user->name,
                    'email' => $order->user->email,
                ] : null,
                'payments' => $order->payments->map(fn ($payment): array => [
                    'id' => $payment->id,
                    'uuid' => $payment->uuid,
                    'amount' => $payment->amount,
                    'gateway' => $payment->gateway,
                    'authority' => $payment->authority,
                    'reference_id' => $payment->reference_id,
                    'status' => $payment->status,
                    'paid_at' => $payment->paid_at?->toIso8601String(),
                    'created_at' => $payment->created_at->toIso8601String(),
                ]),
            ],
        ]);
    }

    /**
     * Manually advance an order's status.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', [
                OrderStatus::Processing->value,
                OrderStatus::Completed->value,
                OrderStatus::Canceled->value,
                OrderStatus::Failed->value,
            ])],
        ], [
            'status.in' => 'وضعیت انتخابی معتبر نیست.',
        ]);

        $order->update([
            'status' => OrderStatus::from($validated['status']),
        ]);

        if (OrderStatus::from($validated['status']) === OrderStatus::Completed) {
            $order->forceFill(['payment_status' => PaymentStatus::Paid])->save();
        }

        return back()->with('success', 'وضعیت سفارش به‌روزرسانی شد.');
    }
}
