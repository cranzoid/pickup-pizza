<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TwoXLPizzasComboSeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = time();
        $productId = DB::table('products')->insertGetId([
            'category_id' => 3, // Combos category
            'name' => 'Two X-Large Pizzas Combo',
            'slug' => "two-xl-pizzas-combo-{$timestamp}",
            'description' => '2 X-Large Pizzas with 3 Toppings each, 3 lb Chicken Wings, and Free Dipping Sauce',
            'price' => 42.99,
            'sizes' => json_encode([
                'xl' => ['price' => 42.99],
            ]),
            'add_ons' => json_encode([
                'third_pizza' => [
                    'name' => 'Add Third Pizza',
                    'description' => 'Add a third X-Large pizza with 3 toppings',
                    'price' => 13.99
                ],
                'extra_wings' => [
                    'name' => 'Additional Wings',
                    'description' => 'Add more wings to your combo for $10.49/lb',
                    'price' => 10.49
                ],
                'pops' => [
                    'name' => '4 Pops',
                    'description' => 'Add 4 pops to your order',
                    'price' => 4.99
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
            'updated_at' => now(),
        ]);

        // Create a combo record too
        DB::table('combos')->insert([
            'product_id' => $productId,
            'name' => 'Two X-Large Pizzas Combo',
            'slug' => "two-xl-pizzas-combo-{$timestamp}",
            'description' => '2 X-Large Pizzas with 3 Toppings each, 3 lb Chicken Wings, and Free Dipping Sauce',
            'add_ons' => json_encode([
                'third_pizza' => [
                    'name' => 'Add Third Pizza',
                    'description' => 'Add a third X-Large pizza with 3 toppings',
                    'price' => 13.99
                ],
                'extra_wings' => [
                    'name' => 'Additional Wings',
                    'description' => 'Add more wings to your combo for $10.49/lb',
                    'price' => 10.49
                ],
                'pops' => [
                    'name' => '4 Pops',
                    'description' => 'Add 4 pops to your order',
                    'price' => 4.99
                ]
            ]),
            'active' => 1,
            'sort_order' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "Created Two X-Large Pizzas Combo product\n";
    }
} 