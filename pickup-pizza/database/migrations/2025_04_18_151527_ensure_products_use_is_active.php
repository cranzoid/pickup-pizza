<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the products table has the 'active' column
        if (Schema::hasColumn('products', 'active') && !Schema::hasColumn('products', 'is_active')) {
            // Only rename if 'active' exists but 'is_active' doesn't
            Schema::table('products', function (Blueprint $table) {
                $table->renameColumn('active', 'is_active');
            });
        } 
        // If both columns exist, ensure data is moved properly
        elseif (Schema::hasColumn('products', 'active') && Schema::hasColumn('products', 'is_active')) {
            // Move data from active to is_active and then drop active
            DB::statement('UPDATE products SET is_active = active');
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('active');
            });
        }
        // If only is_active exists, we're good
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to revert this fix-up migration
    }
};
