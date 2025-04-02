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
        Schema::table('products', function (Blueprint $table) {
            $table->string('display_day')->nullable()->after('sort_order');
            $table->json('preselected_toppings')->nullable()->after('display_day');
            $table->boolean('is_customizable')->default(true)->after('preselected_toppings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('display_day');
            $table->dropColumn('preselected_toppings');
            $table->dropColumn('is_customizable');
        });
    }
};
