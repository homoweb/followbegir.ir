<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('min_quantity');
            $table->unsignedInteger('max_quantity');
            $table->unsignedInteger('price')->comment('Unit price in IRT per 1000 units');
            $table->timestamps();

            $table->index(['product_id', 'min_quantity', 'max_quantity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
