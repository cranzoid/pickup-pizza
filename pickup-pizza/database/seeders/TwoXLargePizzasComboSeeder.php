<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TwoXLargePizzasComboSeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = time();
        $productId = DB::table('products')->insertGetId([
            'category_id' => 3, // Combos category
            'name' => '2 X-Large Pizzas Combo',
            'slug' => "two-xlarge-pizzas-combo-{$timestamp}",
            'description' => '2 X-Large Pizzas (3 toppings each), 3 lbs Wings, 4 Pops, Veggie Sticks, Blue Cheese, Free Dipping Sauce',
            'price' => 56.99,
            'sizes' => json_encode([
                'xlarge' => ['price' => 56.99],
            ]),
            'add_ons' => json_encode([
                'third_pizza' => [
                    'name' => 'Add 3rd Pizza',
                    'description' => 'Add a third X-Large pizza with 3 toppings',
                    'price' => 13.99
                ],
                'garlic_bread' => [
                    'name' => 'Free Garlic Bread',
                    'description' => 'Add free garlic bread to your order',
                    'price' => 0.00,
                    'options' => [
                        'add_cheese' => [
                            'name' => 'Add Cheese',
                            'price' => 1.50
                        ]
                    ]
                ],
                'pop_selection' => [
                    'name' => 'Pop Selection',
                    'description' => 'Choose your 4 included pops',
                    'price' => 0.00,
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
            'has_size_options' => 0,
            'has_toppings' => 1,
            'has_extras' => 1,
            'active' => 1,
            'is_featured' => 1,
            'sort_order' => 3,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create a combo record too
        DB::table('combos')->insert([
            'product_id' => $productId,
            'name' => '2 X-Large Pizzas Combo',
            'slug' => "two-xlarge-pizzas-combo-{$timestamp}",
            'description' => '2 X-Large Pizzas (3 toppings each), 3 lbs Wings, 4 Pops, Veggie Sticks, Blue Cheese, Free Dipping Sauce',
            'add_ons' => json_encode([
                'third_pizza' => [
                    'name' => 'Add 3rd Pizza',
                    'description' => 'Add a third X-Large pizza with 3 toppings',
                    'price' => 13.99
                ],
                'garlic_bread' => [
                    'name' => 'Free Garlic Bread',
                    'description' => 'Add free garlic bread to your order',
                    'price' => 0.00,
                    'options' => [
                        'add_cheese' => [
                            'name' => 'Add Cheese',
                            'price' => 1.50
                        ]
                    ]
                ],
                'pop_selection' => [
                    'name' => 'Pop Selection',
                    'description' => 'Choose your 4 included pops',
                    'price' => 0.00,
                    'options' => [
                        'Coke', 'Pepsi', 'Sprite', 'Diet Coke', 'Diet Pepsi', 
                        'Dr Pepper', 'Orange Crush', 'Cream Soda', 'Brisk Ice Tea', 
                        'Canada Dry', 'Water bottle'
                    ]
                ]
            ]),
            'active' => 1,
            'sort_order' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "Created Two X-Large Pizzas Combo product\n";
    }
} 