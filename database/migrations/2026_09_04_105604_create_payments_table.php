<?php

use App\Enums\PaymentTxnStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('amount')->comment('IRT');
            $table->string('gateway', 32);
            $table->string('authority')->nullable()->comment('Gateway tracking token');
            $table->string('reference_id')->nullable()->comment('Gateway reference after verify');
            $table->string('card_number', 32)->nullable();
            $table->string('status', 16)->default(PaymentTxnStatus::Pending->value);
            $table->text('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index('reference_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
