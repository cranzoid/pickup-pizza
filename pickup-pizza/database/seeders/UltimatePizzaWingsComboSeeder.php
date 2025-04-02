<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UltimatePizzaWingsComboSeeder extends Seeder
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
                'large' => ['price' => 38.99, 'wings' => '3 lbs'],
                'xlarge' => ['price' => 40.99, 'wings' => '3 lbs'],
                'jumbo' => ['price' => 44.99, 'wings' => '3 lbs'],
                'slab' => ['price' => 50.99, 'wings' => '3 lbs'],
            ]),
            'add_ons' => json_encode([
                'second_pizza' => [
                    'name' => 'Add 2nd Pizza',
                    'description' => 'Add a second pizza (only available for Jumbo)',
                    'price' => 15.99,
                    'only_for_sizes' => ['jumbo']
                ],
                'extra_wings' => [
                    'name' => 'Extra Wings',
                    'description' => 'Add more wings to your combo',
                    'price' => 10.49,
                    'per_unit' => 'lb'
                ],
                'pop' => [
                    'name' => 'Add Pop',
                    'description' => 'Add 4 pops to your combo',
                    'price' => 4.99,
                    'options' => [
                        'Coke', 'Pepsi', 'Sprite', 'Diet Coke', 'Diet Pepsi', 
                        'Dr Pepper', 'Orange Crush', 'Cream Soda', 'Brisk Ice Tea', 
                        'Canada Dry', 'Water bottle'
                    ]
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
            'is_featured' => 1,
            'sort_order' => 4,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create a combo record too
        DB::table('combos')->insert([
            'product_id' => $productId,
            'name' => 'Ultimate Pizza & Wings Combo',
            'slug' => "ultimate-pizza-wings-combo-{$timestamp}",
            'description' => '1 Pizza with Cheese + 3 Toppings, Wings (12 for Medium, 3 lbs for others), Veggie Sticks, Blue Cheese, and Free Dipping Sauce',
            'add_ons' => json_encode([
                'second_pizza' => [
                    'name' => 'Add 2nd Pizza',
                    'description' => 'Add a second pizza (only available for Jumbo)',
                    'price' => 15.99,
                    'only_for_sizes' => ['jumbo']
                ],
                'extra_wings' => [
                    'name' => 'Extra Wings',
                    'description' => 'Add more wings to your combo',
                    'price' => 10.49,
                    'per_unit' => 'lb'
                ],
                'pop' => [
                    'name' => 'Add Pop',
                    'description' => 'Add 4 pops to your combo',
                    'price' => 4.99,
                    'options' => [
                        'Coke', 'Pepsi', 'Sprite', 'Diet Coke', 'Diet Pepsi', 
                        'Dr Pepper', 'Orange Crush', 'Cream Soda', 'Brisk Ice Tea', 
                        'Canada Dry', 'Water bottle'
                    ]
                ]
            ]),
            'active' => 1,
            'sort_order' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "Created Ultimate Pizza & Wings Combo product\n";
    }
} 