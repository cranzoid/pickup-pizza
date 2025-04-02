<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SideOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the sides category
        $sidesCategory = Category::where('name', 'Sides')->first();

        if (!$sidesCategory) {
            $sidesCategory = Category::create([
                'name' => 'Sides',
                'slug' => 'sides',
                'description' => 'Delicious sides to complement your pizza.',
                'sort_order' => 4,
                'active' => true,
                'is_daily_special' => false,
            ]);
        }

        // Delete all existing side orders so we can replace with the exact list
        Product::where('category_id', $sidesCategory->id)->delete();

        // Define wing flavor extras for wing products
        $wingFlavors = [
            ['name' => 'Buffalo Hot', 'price' => 0],
            ['name' => 'Buffalo Medium', 'price' => 0],
            ['name' => 'Buffalo Mild', 'price' => 0],
            ['name' => 'BBQ', 'price' => 0],
            ['name' => 'Honey Garlic', 'price' => 0],
            ['name' => 'Salt & Pepper (Dry Rub)', 'price' => 0],
            ['name' => 'Lemon Pepper (Dry Rub)', 'price' => 0],
            ['name' => 'Cajun (Dry Rub)', 'price' => 0]
        ];

        // Define the exact side orders list from the specifications
        $sideOrders = [
            [
                'name' => 'Fried Chicken Dumplings (12 pcs)',
                'description' => 'Delicious fried chicken dumplings',
                'price' => 9.99,
                'sort_order' => 1,
                'has_extras' => false
            ],
            [
                'name' => 'Masala Fries',
                'description' => 'Fries with masala spices',
                'price' => 9.99,
                'sort_order' => 2,
                'has_extras' => false
            ],
            [
                'name' => 'Nachos with Salsa (M)',
                'description' => 'Medium nachos with salsa, lettuce, green pepper, tomatoes',
                'price' => 12.49,
                'sort_order' => 3,
                'has_extras' => false
            ],
            [
                'name' => 'Nachos with Salsa (L)',
                'description' => 'Large nachos with salsa, lettuce, green pepper, tomatoes',
                'price' => 14.49,
                'sort_order' => 4,
                'has_extras' => false
            ],
            [
                'name' => 'Chicken Burger',
                'description' => 'Delicious chicken burger',
                'price' => 6.49,
                'sort_order' => 5,
                'has_extras' => false
            ],
            [
                'name' => 'Chicken Fingers with Fries',
                'description' => 'Crispy chicken fingers served with fries',
                'price' => 8.99,
                'sort_order' => 6,
                'has_extras' => false
            ],
            [
                'name' => 'Buffalo Chicken Wrap',
                'description' => 'Buffalo chicken wrap',
                'price' => 9.99,
                'sort_order' => 7,
                'has_extras' => false
            ],
            [
                'name' => 'Shawarma Style Wrap',
                'description' => 'Shawarma style wrap',
                'price' => 10.99,
                'sort_order' => 8,
                'has_extras' => false
            ],
            [
                'name' => 'Samosa Poutine',
                'description' => 'Poutine with samosa',
                'price' => 11.99,
                'sort_order' => 9,
                'has_extras' => false
            ],
            [
                'name' => 'Chicken Quesadilla',
                'description' => 'Chicken quesadilla',
                'price' => 10.99,
                'sort_order' => 10,
                'has_extras' => false
            ],
            [
                'name' => 'Lasagna',
                'description' => 'Classic lasagna',
                'price' => 10.99,
                'sort_order' => 11,
                'has_extras' => false
            ],
            [
                'name' => 'Wedges',
                'description' => 'Potato wedges',
                'price' => 7.99,
                'sort_order' => 12,
                'has_extras' => false
            ],
            [
                'name' => 'Fries',
                'description' => 'Crispy french fries',
                'price' => 7.99,
                'sort_order' => 13,
                'has_extras' => false
            ],
            [
                'name' => 'Shawarma Poutine',
                'description' => 'Poutine with shawarma',
                'price' => 12.99,
                'sort_order' => 14,
                'has_extras' => false
            ],
            [
                'name' => 'Poutine',
                'description' => 'Classic poutine',
                'price' => 8.99,
                'sort_order' => 15,
                'has_extras' => false
            ],
            [
                'name' => 'Shawarma Sub',
                'description' => 'Shawarma sub sandwich',
                'price' => 9.99,
                'sort_order' => 16,
                'has_extras' => false
            ],
            [
                'name' => 'Meatball Sub',
                'description' => 'Meatball sub sandwich',
                'price' => 8.99,
                'sort_order' => 17,
                'has_extras' => false
            ],
            [
                'name' => 'Pizza 3-Item Sub',
                'description' => '3-Item pizza sub',
                'price' => 8.99,
                'sort_order' => 18,
                'has_extras' => false
            ],
            [
                'name' => '3-Item Panzerotti',
                'description' => 'Panzerotti with 3 items',
                'price' => 11.99,
                'sort_order' => 19,
                'has_extras' => false
            ],
            [
                'name' => 'Garlic Bread',
                'description' => 'Freshly baked garlic bread',
                'price' => 3.30,
                'sort_order' => 20,
                'has_extras' => false
            ],
            [
                'name' => 'Garlic Bread with Cheese',
                'description' => 'Freshly baked garlic bread with cheese',
                'price' => 4.49,
                'sort_order' => 21,
                'has_extras' => false
            ],
            [
                'name' => 'Greek Salad',
                'description' => 'Greek salad with feta cheese',
                'price' => 9.49,
                'sort_order' => 22,
                'has_extras' => false
            ],
            [
                'name' => 'Caesar Salad',
                'description' => 'Classic caesar salad',
                'price' => 9.49,
                'sort_order' => 23,
                'has_extras' => false
            ],
            [
                'name' => 'Onion Rings',
                'description' => 'Crispy onion rings',
                'price' => 7.99,
                'sort_order' => 24,
                'has_extras' => false
            ],
            [
                'name' => '12 Wings',
                'description' => '12 pieces of wings',
                'price' => 14.99,
                'sort_order' => 25,
                'has_extras' => true
            ],
            [
                'name' => 'Stuffed Jalapeños (6 pcs)',
                'description' => '6 stuffed jalapeños',
                'price' => 7.99,
                'sort_order' => 26,
                'has_extras' => false
            ],
            [
                'name' => 'Mozzarella Sticks (6 pcs)',
                'description' => '6 mozzarella sticks',
                'price' => 7.99,
                'sort_order' => 27,
                'has_extras' => false
            ]
        ];
        
        // Create side order products
        foreach ($sideOrders as $side) {
            $product = Product::create([
                'category_id' => $sidesCategory->id,
                'name' => $side['name'],
                'slug' => Str::slug($side['name']),
                'description' => $side['description'],
                'price' => $side['price'],
                'is_pizza' => false,
                'is_specialty' => false,
                'has_size_options' => false,
                'has_toppings' => false,
                'has_extras' => $side['has_extras'],
                'active' => true,
                'sort_order' => $side['sort_order'],
            ]);
            
            // Add wing flavor extras to wing products
            if (strpos($side['name'], 'Wings') !== false) {
                foreach ($wingFlavors as $index => $flavor) {
                    $extra = \App\Models\ProductExtra::create([
                        'name' => $flavor['name'],
                        'price' => $flavor['price'],
                        'description' => '',
                        'is_default' => $index === 1, // Default to Buffalo Medium
                        'max_quantity' => 1,
                        'active' => true,
                    ]);
                    
                    // Attach the extra to the product using the pivot table
                    $product->extras()->attach($extra->id);
                }
            }
        }

        $this->command->info('Side orders updated successfully!');
    }
} 