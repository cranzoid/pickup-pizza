<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UltimateComboSeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = time();
        $productId = DB::table('products')->insertGetId([
            'category_id' => 3, // Combos category
            'name' => 'Ultimate Pizza & Wings Combo',
            'slug' => "ultimate-pizza-wings-combo-{$timestamp}",
            'description' => '1 Pizza with Cheese + 3 Toppings, Wings (12 for Medium, 3 lbs for others), Veggie Sticks, Blue Cheese, and Free Dipping Sauce',
            'price' => 27.99,
            'sizes' => json_encode([
                'medium' => ['price' => 27.99, 'wings' => 12],
                'large' => ['price' => 38.99, 'wings' => 36],
                'xl' => ['price' => 40.99, 'wings' => 36],
                'jumbo' => ['price' => 44.99, 'wings' => 36],
                'slab' => ['price' => 50.99, 'wings' => 36],
            ]),
            'add_ons' => json_encode([
                'extra_wings' => [
                    'name' => 'Additional Wings',
                    'description' => 'Add more wings to your combo',
                    'price' => 12.99
                ],
                'pop' => [
                    'name' => '2L Pop',
                    'description' => 'Add a 2L pop to your combo',
                    'price' => 3.99
                ]
            ]),
            'max_toppings' => 3,
            'free_toppings' => 3,
            'is_pizza' => 1,
            'is_specialty' => 0,
            'has_size_options' => 1,
            'has_toppings' => 1,
            'has_extras' => 1,
            'active' => 1,
            'is_featured' => 0,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create a combo record too
        DB::table('combos')->insert([
            'product_id' => $productId,
            'name' => 'Ultimate Pizza & Wings Combo',
            'slug' => "ultimate-pizza-wings-combo-{$timestamp}",
            'description' => '1 Pizza with Cheese + 3 Toppings, Wings (12 for Medium, 3 lbs for others), Veggie Sticks, Blue Cheese, and Free Dipping Sauce',
            'add_ons' => json_encode([
                'extra_wings' => [
                    'name' => 'Additional Wings',
                    'description' => 'Add more wings to your combo',
                    'price' => 12.99
                ],
                'pop' => [
                    'name' => '2L Pop',
                    'description' => 'Add a 2L pop to your combo',
                    'price' => 3.99
                ]
            ]),
            'active' => 1,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "Created Ultimate Pizza & Wings Combo product\n";
    }
} 