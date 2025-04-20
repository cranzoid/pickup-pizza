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
        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('active', 'is_active');
            $table->integer('display_order')->default(0)->after('sort_order');
            $table->boolean('day_specific')->default(false)->after('is_daily_special');
            $table->string('specific_day')->nullable()->after('day_specific');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('is_active', 'active');
            $table->dropColumn(['display_order', 'day_specific', 'specific_day']);
        });
    }
}; 