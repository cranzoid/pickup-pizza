<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixComboCategories extends Seeder
{
    public function run(): void
    {
        // Get combo category ID
        $combosCategoryId = 6; // Based on our category listing

        // 1. Remove the specified combos
        $combosToRemove = [
            'Pizza & Wings Combo',
            'Family Feast',
            'Party Pack',
            'Student Special',
            'Wing Lovers'
        ];

        // Remove from products table
        DB::table('products')
            ->whereIn('name', $combosToRemove)
            ->delete();

        // Get IDs of combos to remove
        $comboIds = DB::table('combos')
            ->whereIn('name', $combosToRemove)
            ->pluck('id')
            ->toArray();

        // Remove from combo_product table
        DB::table('combo_product')
            ->whereIn('combo_id', $comboIds)
            ->delete();

        // Remove from combos table
        DB::table('combos')
            ->whereIn('name', $combosToRemove)
            ->delete();

        // 2. Fix category ID for remaining combos
        // Update category_id in products table for the remaining combos
        $remainingCombos = [
            'Two Medium Pizzas Combo',
            'Two Large Pizzas Combo',
            'Two X-Large Pizzas Combo',
            'Two XL Pizzas Combo',
            'Ultimate Pizza & Wings Combo'
        ];

        DB::table('products')
            ->whereIn('name', $remainingCombos)
            ->update(['category_id' => $combosCategoryId]);

        echo "Removed unwanted combos and fixed category IDs for remaining ones.\n";
    }
} 