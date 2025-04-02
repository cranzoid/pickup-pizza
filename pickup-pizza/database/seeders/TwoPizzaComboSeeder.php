<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TwoPizzaComboSeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = time();
        
        // Two Medium Pizzas Combo
        $mediumComboId = DB::table('products')->insertGetId([
            'category_id' => 3, // Combos category
            'name' => 'Two Medium Pizzas Combo',
            'slug' => "two-medium-pizzas-combo-{$timestamp}",
            'description' => 'Two Medium Pizzas with 3 toppings each, 2lb Chicken Wings, 4 Pops, Free Garlic Bread',
            'price' => 29.99,
            'sizes' => json_encode([
                'medium' => ['price' => 29.99, 'wings' => 32], // 2lb wings = 32oz
            ]),
            'add_ons' => json_encode([
                'extra_wings' => [
                    'name' => 'Additional Wings',
                    'description' => 'Add more wings to your combo',
                    'price' => 10.49
                ],
                'third_pizza' => [
                    'name' => 'Third Medium Pizza',
                    'description' => 'Add a third medium pizza to your combo',
                    'price' => 10.99
                ]
            ]),
            'max_toppings' => 3,
            'free_toppings' => 3,
            'is_pizza' => 1,
            'is_specialty' => 0,
            'has_size_options' => 0,
            'has_toppings' => 1,
            'has_extras' => 1,
            'active' => 1,
            'is_featured' => 0,
            'sort_order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create a combo record too
        DB::table('combos')->insert([
            'product_id' => $mediumComboId,
            'name' => 'Two Medium Pizzas Combo',
            'slug' => "two-medium-pizzas-combo-{$timestamp}",
            'description' => 'Two Medium Pizzas with 3 toppings each, 2lb Chicken Wings, 4 Pops, Free Garlic Bread',
            'add_ons' => json_encode([
                'extra_wings' => [
                    'name' => 'Additional Wings',
                    'description' => 'Add more wings to your combo',
                    'price' => 10.49
                ],
                'third_pizza' => [
                    'name' => 'Third Medium Pizza',
                    'description' => 'Add a third medium pizza to your combo',
                    'price' => 10.99
                ]
            ]),
            'active' => 1,
            'sort_order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Two Large Pizzas Combo
        $largeComboId = DB::table('products')->insertGetId([
            'category_id' => 3, // Combos category
            'name' => 'Two Large Pizzas Combo',
            'slug' => "two-large-pizzas-combo-{$timestamp}",
            'description' => 'Two Large Pizzas with 3 toppings each, 3lb Chicken Wings, 4 Pops, Free Garlic Bread',
            'price' => 39.99,
            'sizes' => json_encode([
                'large' => ['price' => 39.99, 'wings' => 48], // 3lb wings = 48oz
            ]),
            'add_ons' => json_encode([
                'extra_wings' => [
                    'name' => 'Additional Wings',
                    'description' => 'Add more wings to your combo',
                    'price' => 10.49
                ],
                'third_pizza' => [
                    'name' => 'Third Large Pizza',
                    'description' => 'Add a third large pizza to your combo',
                    'price' => 12.99
                ]
            ]),
            'max_toppings' => 3,
            'free_toppings' => 3,
            'is_pizza' => 1,
            'is_specialty' => 0,
            'has_size_options' => 0,
            'has_toppings' => 1,
            'has_extras' => 1,
            'active' => 1,
            'is_featured' => 0,
            'sort_order' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create a combo record too
        DB::table('combos')->insert([
            'product_id' => $largeComboId,
            'name' => 'Two Large Pizzas Combo',
            'slug' => "two-large-pizzas-combo-{$timestamp}",
            'description' => 'Two Large Pizzas with 3 toppings each, 3lb Chicken Wings, 4 Pops, Free Garlic Bread',
            'add_ons' => json_encode([
                'extra_wings' => [
                    'name' => 'Additional Wings',
                    'description' => 'Add more wings to your combo',
                    'price' => 10.49
                ],
                'third_pizza' => [
                    'name' => 'Third Large Pizza',
                    'description' => 'Add a third large pizza to your combo',
                    'price' => 12.99
                ]
            ]),
            'active' => 1,
            'sort_order' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Two XL Pizzas Combo
        $xlComboId = DB::table('products')->insertGetId([
            'category_id' => 3, // Combos category
            'name' => 'Two XL Pizzas Combo',
            'slug' => "two-xl-pizzas-combo-{$timestamp}",
            'description' => 'Two XL Pizzas with 3 toppings each, 3lb Chicken Wings, 4 Pops, Free Garlic Bread',
            'price' => 45.99,
            'sizes' => json_encode([
                'xl' => ['price' => 45.99, 'wings' => 48], // 3lb wings = 48oz
            ]),
            'add_ons' => json_encode([
                'extra_wings' => [
                    'name' => 'Additional Wings',
                    'description' => 'Add more wings to your combo',
                    'price' => 10.49
                ],
                'third_pizza' => [
                    'name' => 'Third XL Pizza',
                    'description' => 'Add a third XL pizza to your combo',
                    'price' => 13.99
                ]
            ]),
            'max_toppings' => 3,
            'free_toppings' => 3,
            'is_pizza' => 1,
            'is_specialty' => 0,
            'has_size_options' => 0,
            'has_toppings' => 1,
            'has_extras' => 1,
            'active' => 1,
            'is_featured' => 0,
            'sort_order' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create a combo record too
        DB::table('combos')->insert([
            'product_id' => $xlComboId,
            'name' => 'Two XL Pizzas Combo',
            'slug' => "two-xl-pizzas-combo-{$timestamp}",
            'description' => 'Two XL Pizzas with 3 toppings each, 3lb Chicken Wings, 4 Pops, Free Garlic Bread',
            'add_ons' => json_encode([
                'extra_wings' => [
                    'name' => 'Additional Wings',
                    'description' => 'Add more wings to your combo',
                    'price' => 10.49
                ],
                'third_pizza' => [
                    'name' => 'Third XL Pizza',
                    'description' => 'Add a third XL pizza to your combo',
                    'price' => 13.99
                ]
            ]),
            'active' => 1,
            'sort_order' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "Created Two Pizza Combo products\n";
    }
} 