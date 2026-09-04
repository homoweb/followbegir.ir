<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->string('order_number', 24)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Immutable snapshot of the purchased product
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('product_type', 32);
            $table->string('product_platform', 32);
            $table->string('product_title');
            $table->string('target_username', 128);
            $table->unsignedBigInteger('quantity');
            $table->unsignedInteger('unit_price')->comment('IRT per 1000 units, snapshot');
            $table->unsignedBigInteger('total_price')->comment('IRT, snapshot');

            $table->string('status', 24)->default(OrderStatus::Pending->value);
            $table->string('payment_status', 24)->default(PaymentStatus::Unpaid->value);

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('status');
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
