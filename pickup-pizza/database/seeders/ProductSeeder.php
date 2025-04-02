<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Topping;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $specialtyCategory = Category::where('name', 'Specialty Pizzas')->first();
        $buildYourOwnCategory = Category::where('name', 'Build Your Own Pizza')->first();
        $wingsCategory = Category::where('name', 'Wings')->first();
        $sidesCategory = Category::where('name', 'Sides')->first();
        $drinksCategory = Category::where('name', 'Drinks')->first();
        
        // Get toppings
        $pepperoni = Topping::where('name', 'Pepperoni')->first();
        $mushrooms = Topping::where('name', 'Mushrooms')->first();
        $greenPeppers = Topping::where('name', 'Green Peppers')->first();
        $onions = Topping::where('name', 'Onions')->first();
        $bacon = Topping::where('name', 'Bacon')->first();
        $extraCheese = Topping::where('name', 'Extra Cheese')->first();
        $italianSausage = Topping::where('name', 'Italian Sausage')->first();
        $pineapple = Topping::where('name', 'Pineapple')->first();
        $ham = Topping::where('name', 'Ham')->first();
        
        // Create Specialty Pizzas
        $specialtyPizzas = [
            [
                'name' => 'Deluxe',
                'description' => 'Loaded with pepperoni, Italian sausage, green peppers, mushrooms, and onions.',
                'price' => 0, // Base price, actual prices are in sizes
                'sizes' => [
                    'medium' => 15.99,
                    'large' => 18.99,
                    'xl' => 21.99,
                    'jumbo' => 25.99
                ],
                'toppings' => [
                    $pepperoni, $italianSausage, $greenPeppers, $mushrooms, $onions
                ],
                'sort_order' => 1
            ],
            [
                'name' => 'Meat Lovers',
                'description' => 'For the carnivore! Loaded with pepperoni, Italian sausage, bacon, ham, and ground beef.',
                'price' => 0,
                'sizes' => [
                    'medium' => 16.99,
                    'large' => 19.99,
                    'xl' => 22.99,
                    'jumbo' => 26.99
                ],
                'toppings' => [
                    $pepperoni, $italianSausage, $bacon, $ham, Topping::where('name', 'Ground Beef')->first()
                ],
                'sort_order' => 2
            ],
            [
                'name' => 'Hawaiian',
                'description' => 'A tropical treat with ham, bacon, and pineapple.',
                'price' => 0,
                'sizes' => [
                    'medium' => 14.99,
                    'large' => 17.99,
                    'xl' => 20.99,
                    'jumbo' => 24.99
                ],
                'toppings' => [
                    $ham, $bacon, $pineapple
                ],
                'sort_order' => 3
            ],
            [
                'name' => 'Veggie',
                'description' => 'Packed with mushrooms, green peppers, onions, tomatoes, and black olives.',
                'price' => 0,
                'sizes' => [
                    'medium' => 14.99,
                    'large' => 17.99,
                    'xl' => 20.99,
                    'jumbo' => 24.99
                ],
                'toppings' => [
                    $mushrooms, $greenPeppers, $onions, Topping::where('name', 'Tomatoes')->first(), Topping::where('name', 'Black Olives')->first()
                ],
                'sort_order' => 4
            ]
        ];
        
        // Add specialty pizzas
        foreach ($specialtyPizzas as $pizza) {
            $product = Product::create([
                'category_id' => $specialtyCategory->id,
                'name' => $pizza['name'],
                'slug' => Str::slug($pizza['name']),
                'description' => $pizza['description'],
                'price' => $pizza['price'],
                'sizes' => $pizza['sizes'],
                'max_toppings' => 0, // Specialty pizzas don't allow topping customization
                'is_pizza' => true,
                'is_specialty' => true,
                'has_size_options' => true,
                'has_toppings' => true,
                'active' => true,
                'sort_order' => $pizza['sort_order'],
            ]);
            
            // Add default toppings for specialty pizzas
            foreach ($pizza['toppings'] as $topping) {
                $product->toppings()->attach($topping->id, [
                    'is_default' => true,
                    'quantity' => 1,
                ]);
            }
        }
        
        // Create Build Your Own Pizza
        $buildYourOwn = Product::create([
            'category_id' => $buildYourOwnCategory->id,
            'name' => 'Build Your Own Pizza',
            'slug' => 'build-your-own-pizza',
            'description' => 'Create your own pizza with your choice of toppings. Price includes one topping.',
            'price' => 0,
            'sizes' => [
                'medium' => 12.99,
                'large' => 14.99,
                'xl' => 16.99,
                'jumbo' => 19.99
            ],
            'max_toppings' => 10, // Upper limit on toppings
            'is_pizza' => true,
            'is_specialty' => false,
            'has_size_options' => true,
            'has_toppings' => true,
            'active' => true,
            'sort_order' => 1,
        ]);
        
        // Create Wings
        $wingSizes = [
            '1 lb' => 10.99,
            '2 lbs' => 19.99,
            '3 lbs' => 28.99,
        ];
        
        $wings = Product::create([
            'category_id' => $wingsCategory->id,
            'name' => 'Jumbo Chicken Wings',
            'slug' => 'jumbo-chicken-wings',
            'description' => 'Our jumbo chicken wings with your choice of sauce or rub. Served with ranch or blue cheese dip.',
            'price' => 10.99, // Base price for 1 lb
            'sizes' => $wingSizes,
            'max_toppings' => 0,
            'is_pizza' => false,
            'is_specialty' => false,
            'has_size_options' => true,
            'has_toppings' => false,
            'active' => true,
            'sort_order' => 1,
        ]);
        
        // Create Garlic Bread
        $garlicBread = Product::create([
            'category_id' => $sidesCategory->id,
            'name' => 'Garlic Bread',
            'slug' => 'garlic-bread',
            'description' => 'Freshly baked bread with garlic butter. Add cheese for an extra $1.50.',
            'price' => 4.99,
            'sizes' => null,
            'max_toppings' => 0,
            'is_pizza' => false,
            'is_specialty' => false,
            'has_size_options' => false,
            'has_toppings' => false,
            'active' => true,
            'sort_order' => 1,
        ]);
        
        // Create Cheesy Garlic Bread
        $cheesyGarlicBread = Product::create([
            'category_id' => $sidesCategory->id,
            'name' => 'Cheesy Garlic Bread',
            'slug' => 'cheesy-garlic-bread',
            'description' => 'Freshly baked bread with garlic butter and melted mozzarella cheese.',
            'price' => 6.49,
            'sizes' => null,
            'max_toppings' => 0,
            'is_pizza' => false,
            'is_specialty' => false,
            'has_size_options' => false,
            'has_toppings' => false,
            'active' => true,
            'sort_order' => 2,
        ]);
        
        // Create Pop
        $pop = Product::create([
            'category_id' => $drinksCategory->id,
            'name' => 'Pop',
            'slug' => 'pop',
            'description' => 'Choose from Coke, Diet Coke, Sprite, or Ginger Ale.',
            'price' => 1.99,
            'sizes' => [
                'can' => 1.99,
                '2L bottle' => 3.49,
                '4-pack' => 4.99
            ],
            'max_toppings' => 0,
            'is_pizza' => false,
            'is_specialty' => false,
            'has_size_options' => true,
            'has_toppings' => false,
            'active' => true,
            'sort_order' => 1,
        ]);
    }
}
