<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('price', 8, 2)->default(0);
            $table->json('sizes')->nullable(); // Store sizes and their prices
            $table->integer('max_toppings')->nullable();
            $table->integer('free_toppings')->nullable()->default(0);
            $table->boolean('is_pizza')->default(false);
            $table->boolean('is_specialty')->default(false); // Specialty pizza can't modify toppings
            $table->boolean('has_size_options')->default(false);
            $table->boolean('has_toppings')->default(false);
            $table->boolean('has_extras')->default(false);
            $table->boolean('active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
