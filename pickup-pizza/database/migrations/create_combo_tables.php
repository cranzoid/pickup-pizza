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
        // Skip creation if tables already exist
        if (Schema::hasTable('combos')) {
            return;
        }

        Schema::create('combos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->decimal('regular_price', 8, 2)->nullable();
            $table->string('image')->nullable();
            $table->boolean('active')->default(true);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        if (!Schema::hasTable('combo_product')) {
            Schema::create('combo_product', function (Blueprint $table) {
                $table->id();
                $table->foreignId('combo_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->integer('quantity')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('combo_upsell_product')) {
            Schema::create('combo_upsell_product', function (Blueprint $table) {
                $table->id();
                $table->foreignId('combo_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                
                $table->unique(['combo_id', 'product_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combo_upsell_product');
        Schema::dropIfExists('combo_product');
        Schema::dropIfExists('combos');
    }
}; 