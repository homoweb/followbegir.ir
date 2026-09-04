<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 32)->comment('ProductType enum');
            $table->string('platform', 32)->comment('Platform enum');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('min_quantity');
            $table->integer('max_quantity');
            $table->integer('step_quantity')->default(1000);
            $table->integer('base_price')->comment('Fallback unit price in IRT per 1000 units');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['platform', 'type'], 'products_platform_type_unique');
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
