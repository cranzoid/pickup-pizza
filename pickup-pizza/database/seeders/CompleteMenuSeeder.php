<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Combo;
use App\Models\ComboProduct;
use App\Models\ProductExtra;
use App\Models\Topping;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CompleteMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $specialtyCategory = Category::where('name', 'Specialty Pizzas')->first();
        $buildYourOwnCategory = Category::where('name', 'Build Your Own Pizza')->first();
        $comboCategory = Category::where('name', 'Combos')->first();
        $wingsCategory = Category::where('name', 'Wings')->first();
        $sidesCategory = Category::where('name', 'Sides')->first();
        $drinksCategory = Category::where('name', 'Drinks')->first();
        $mondaySpecialCategory = Category::where('name', 'Monday Special')->first();
        $tuesdaySpecialCategory = Category::where('name', 'Tuesday Special')->first();
        $wednesdaySpecialCategory = Category::where('name', 'Wednesday Special')->first();
        $thursdaySpecialCategory = Category::where('name', 'Thursday Special')->first();
        $weekendSpecialCategory = Category::where('name', 'Weekend Special')->first();
        
        // Get toppings for reference
        $toppings = Topping::all()->keyBy('name');
        
        // =====================================================================
        // SPECIALTY PIZZAS
        // =====================================================================
        $specialtyPizzas = [
            [
                'name' => 'Deluxe',
                'description' => 'Loaded with pepperoni, Italian sausage, green peppers, mushrooms, and onions.',
                'price' => 0,
                'sizes' => [
                    'medium' => 15.99,
                    'large' => 18.99,
                    'xl' => 21.99,
                    'jumbo' => 25.99
                ],
                'toppings' => ['Pepperoni', 'Italian Sausage', 'Green Peppers', 'Mushrooms', 'Onions'],
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
                'toppings' => ['Pepperoni', 'Italian Sausage', 'Bacon', 'Ham', 'Ground Beef'],
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
                'toppings' => ['Ham', 'Bacon', 'Pineapple'],
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
                'toppings' => ['Mushrooms', 'Green Peppers', 'Onions', 'Tomatoes', 'Black Olives'],
                'sort_order' => 4
            ],
            [
                'name' => 'BBQ Chicken',
                'description' => 'Real chicken, red onions, and bacon with BBQ sauce.',
                'price' => 0,
                'sizes' => [
                    'medium' => 16.99,
                    'large' => 19.99,
                    'xl' => 22.99,
                    'jumbo' => 26.99
                ],
                'toppings' => ['Real Chicken', 'Onions', 'Bacon'],
                'sort_order' => 5
            ],
            [
                'name' => 'Mediterranean',
                'description' => 'Feta cheese, spinach, tomatoes, olives, and roasted red peppers.',
                'price' => 0,
                'sizes' => [
                    'medium' => 15.99,
                    'large' => 18.99,
                    'xl' => 21.99,
                    'jumbo' => 25.99
                ],
                'toppings' => ['Feta Cheese', 'Spinach', 'Tomatoes', 'Black Olives', 'Roasted Red Peppers'],
                'sort_order' => 6
            ],
            [
                'name' => 'Supreme',
                'description' => 'The ultimate pizza experience with pepperoni, sausage, mushrooms, onions, green peppers, and black olives.',
                'price' => 0,
                'sizes' => [
                    'medium' => 16.99,
                    'large' => 19.99,
                    'xl' => 22.99,
                    'jumbo' => 26.99
                ],
                'toppings' => ['Pepperoni', 'Italian Sausage', 'Mushrooms', 'Onions', 'Green Peppers', 'Black Olives'],
                'sort_order' => 7
            ],
            [
                'name' => 'Buffalo Chicken',
                'description' => 'Real chicken, green peppers, and onions with buffalo sauce and a ranch drizzle.',
                'price' => 0,
                'sizes' => [
                    'medium' => 16.99,
                    'large' => 19.99,
                    'xl' => 22.99,
                    'jumbo' => 26.99
                ],
                'toppings' => ['Real Chicken', 'Green Peppers', 'Onions'],
                'sort_order' => 8
            ]
        ];
        
        foreach ($specialtyPizzas as $pizza) {
            $product = Product::create([
                'category_id' => $specialtyCategory->id,
                'name' => $pizza['name'],
                'slug' => Str::slug($pizza['name']),
                'description' => $pizza['description'],
                'price' => $pizza['price'],
                'sizes' => $pizza['sizes'],
                'max_toppings' => 0,
                'is_pizza' => true,
                'is_specialty' => true,
                'has_size_options' => true,
                'has_toppings' => true,
                'active' => true,
                'sort_order' => $pizza['sort_order'],
            ]);
            
            foreach ($pizza['toppings'] as $toppingName) {
                if (isset($toppings[$toppingName])) {
                    $product->toppings()->attach($toppings[$toppingName]->id, [
                        'is_default' => true,
                        'quantity' => 1,
                    ]);
                }
            }
        }

        // =====================================================================
        // BUILD YOUR OWN PIZZA
        // =====================================================================
        Product::create([
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
            'max_toppings' => 10,
            'is_pizza' => true,
            'is_specialty' => false,
            'has_size_options' => true,
            'has_toppings' => true,
            'active' => true,
            'sort_order' => 1,
        ]);

        // =====================================================================
        // COMBOS
        // =====================================================================
        $combos = [
            [
                'name' => 'Family Feast',
                'description' => '2 Large 2-topping pizzas, 1 lb of wings, and a 2L pop',
                'price' => 39.99,
                'products' => [
                    [
                        'name' => 'Large 2-Topping Pizza',
                        'quantity' => 2,
                        'max_toppings' => 2,
                        'is_pizza' => true
                    ],
                    [
                        'name' => 'Jumbo Chicken Wings (1 lb)',
                        'quantity' => 1,
                        'max_toppings' => 0,
                        'is_pizza' => false
                    ],
                    [
                        'name' => 'Pop (2L bottle)',
                        'quantity' => 1,
                        'max_toppings' => 0,
                        'is_pizza' => false
                    ]
                ],
                'sort_order' => 1
            ],
            [
                'name' => 'Date Night Special',
                'description' => '1 Medium specialty pizza, 1 order of cheesy garlic bread, and 2 cans of pop',
                'price' => 24.99,
                'products' => [
                    [
                        'name' => 'Medium Specialty Pizza',
                        'quantity' => 1,
                        'max_toppings' => 0,
                        'is_pizza' => true
                    ],
                    [
                        'name' => 'Cheesy Garlic Bread',
                        'quantity' => 1,
                        'max_toppings' => 0,
                        'is_pizza' => false
                    ],
                    [
                        'name' => 'Pop (can)',
                        'quantity' => 2,
                        'max_toppings' => 0,
                        'is_pizza' => false
                    ]
                ],
                'sort_order' => 2
            ],
            [
                'name' => 'Party Pack',
                'description' => '2 XL 3-topping pizzas, 2 lbs of wings, and a 4-pack of pop',
                'price' => 49.99,
                'products' => [
                    [
                        'name' => 'XL 3-Topping Pizza',
                        'quantity' => 2,
                        'max_toppings' => 3,
                        'is_pizza' => true
                    ],
                    [
                        'name' => 'Jumbo Chicken Wings (2 lbs)',
                        'quantity' => 1,
                        'max_toppings' => 0,
                        'is_pizza' => false
                    ],
                    [
                        'name' => 'Pop (4-pack)',
                        'quantity' => 1,
                        'max_toppings' => 0,
                        'is_pizza' => false
                    ]
                ],
                'sort_order' => 3
            ],
            [
                'name' => 'Game Day Special',
                'description' => '1 Jumbo 1-topping pizza, 2 lbs of wings, and a 2L pop',
                'price' => 34.99,
                'products' => [
                    [
                        'name' => 'Jumbo 1-Topping Pizza',
                        'quantity' => 1,
                        'max_toppings' => 1,
                        'is_pizza' => true
                    ],
                    [
                        'name' => 'Jumbo Chicken Wings (2 lbs)',
                        'quantity' => 1,
                        'max_toppings' => 0,
                        'is_pizza' => false
                    ],
                    [
                        'name' => 'Pop (2L bottle)',
                        'quantity' => 1,
                        'max_toppings' => 0,
                        'is_pizza' => false
                    ]
                ],
                'sort_order' => 4
            ]
        ];

        foreach ($combos as $comboData) {
            $combo = Combo::create([
                'category_id' => $comboCategory->id,
                'name' => $comboData['name'],
                'slug' => Str::slug($comboData['name']),
                'description' => $comboData['description'],
                'price' => $comboData['price'],
                'active' => true,
                'sort_order' => $comboData['sort_order'],
            ]);
            
            foreach ($comboData['products'] as $productData) {
                ComboProduct::create([
                    'combo_id' => $combo->id,
                    'name' => $productData['name'],
                    'quantity' => $productData['quantity'],
                    'max_toppings' => $productData['max_toppings'],
                    'is_pizza' => $productData['is_pizza'],
                ]);
            }
        }

        // =====================================================================
        // WINGS
        // =====================================================================
        $wings = Product::create([
            'category_id' => $wingsCategory->id,
            'name' => 'Jumbo Chicken Wings',
            'slug' => 'jumbo-chicken-wings',
            'description' => 'Our jumbo chicken wings with your choice of sauce or rub. Served with ranch or blue cheese dip.',
            'price' => 10.99,
            'sizes' => [
                '1 lb' => 10.99,
                '2 lbs' => 19.99,
                '3 lbs' => 28.99,
            ],
            'max_toppings' => 0,
            'is_pizza' => false,
            'is_specialty' => false,
            'has_size_options' => true,
            'has_toppings' => false,
            'active' => true,
            'sort_order' => 1,
        ]);
        
        // Add wing flavors/sauces as extras
        $wingExtras = [
            ['name' => 'Buffalo Hot', 'price' => 0, 'sort_order' => 1],
            ['name' => 'Buffalo Medium', 'price' => 0, 'sort_order' => 2],
            ['name' => 'Buffalo Mild', 'price' => 0, 'sort_order' => 3],
            ['name' => 'BBQ', 'price' => 0, 'sort_order' => 4],
            ['name' => 'Honey Garlic', 'price' => 0, 'sort_order' => 5],
            ['name' => 'Salt & Pepper Dry Rub', 'price' => 0, 'sort_order' => 6],
            ['name' => 'Lemon Pepper Dry Rub', 'price' => 0, 'sort_order' => 7],
            ['name' => 'Cajun Dry Rub', 'price' => 0, 'sort_order' => 8],
        ];
        
        foreach ($wingExtras as $extra) {
            ProductExtra::create([
                'product_id' => $wings->id,
                'name' => $extra['name'],
                'price' => $extra['price'],
                'sort_order' => $extra['sort_order'],
            ]);
        }

        // =====================================================================
        // SIDES
        // =====================================================================
        $sides = [
            [
                'name' => 'Fried Chicken Dumplings (12 pcs)',
                'description' => 'Delicious fried chicken dumplings',
                'price' => 9.99,
                'sort_order' => 1
            ],
            [
                'name' => 'Masala Fries',
                'description' => 'Fries with masala spices',
                'price' => 9.99,
                'sort_order' => 2
            ],
            [
                'name' => 'Nachos with Salsa (M)',
                'description' => 'Medium nachos with salsa, lettuce, green pepper, tomatoes',
                'price' => 12.49,
                'sort_order' => 3
            ],
            [
                'name' => 'Nachos with Salsa (L)',
                'description' => 'Large nachos with salsa, lettuce, green pepper, tomatoes',
                'price' => 14.49,
                'sort_order' => 4
            ],
            [
                'name' => 'Chicken Burger',
                'description' => 'Delicious chicken burger',
                'price' => 6.49,
                'sort_order' => 5
            ],
            [
                'name' => 'Chicken Fingers with Fries',
                'description' => 'Crispy chicken fingers served with fries',
                'price' => 8.99,
                'sort_order' => 6
            ],
            [
                'name' => 'Buffalo Chicken Wrap',
                'description' => 'Buffalo chicken wrap',
                'price' => 9.99,
                'sort_order' => 7
            ],
            [
                'name' => 'Shawarma Style Wrap',
                'description' => 'Shawarma style wrap',
                'price' => 10.99,
                'sort_order' => 8
            ],
            [
                'name' => 'Samosa Poutine',
                'description' => 'Poutine with samosa',
                'price' => 11.99,
                'sort_order' => 9
            ],
            [
                'name' => 'Chicken Quesadilla',
                'description' => 'Chicken quesadilla',
                'price' => 10.99,
                'sort_order' => 10
            ],
            [
                'name' => 'Lasagna',
                'description' => 'Classic lasagna',
                'price' => 10.99,
                'sort_order' => 11
            ],
            [
                'name' => 'Wedges',
                'description' => 'Potato wedges',
                'price' => 7.99,
                'sort_order' => 12
            ],
            [
                'name' => 'Fries',
                'description' => 'Crispy french fries',
                'price' => 7.99,
                'sort_order' => 13
            ],
            [
                'name' => 'Shawarma Poutine',
                'description' => 'Poutine with shawarma',
                'price' => 12.99,
                'sort_order' => 14
            ],
            [
                'name' => 'Poutine',
                'description' => 'Classic poutine',
                'price' => 8.99,
                'sort_order' => 15
            ],
            [
                'name' => 'Shawarma Sub',
                'description' => 'Shawarma sub sandwich',
                'price' => 9.99,
                'sort_order' => 16
            ],
            [
                'name' => 'Meatball Sub',
                'description' => 'Meatball sub sandwich',
                'price' => 8.99,
                'sort_order' => 17
            ],
            [
                'name' => 'Pizza 3-Item Sub',
                'description' => '3-Item pizza sub',
                'price' => 8.99,
                'sort_order' => 18
            ],
            [
                'name' => '3-Item Panzerotti',
                'description' => 'Panzerotti with 3 items',
                'price' => 11.99,
                'sort_order' => 19
            ],
            [
                'name' => 'Garlic Bread',
                'description' => 'Freshly baked garlic bread',
                'price' => 3.30,
                'sort_order' => 20
            ],
            [
                'name' => 'Garlic Bread with Cheese',
                'description' => 'Freshly baked garlic bread with cheese',
                'price' => 4.49,
                'sort_order' => 21
            ],
            [
                'name' => 'Greek Salad',
                'description' => 'Greek salad with feta cheese',
                'price' => 9.49,
                'sort_order' => 22
            ],
            [
                'name' => 'Caesar Salad',
                'description' => 'Classic caesar salad',
                'price' => 9.49,
                'sort_order' => 23
            ],
            [
                'name' => 'Onion Rings',
                'description' => 'Crispy onion rings',
                'price' => 7.99,
                'sort_order' => 24
            ],
            [
                'name' => '12 Wings',
                'description' => '12 pieces of wings',
                'price' => 14.99,
                'sort_order' => 25
            ],
            [
                'name' => 'Stuffed Jalapeños (6 pcs)',
                'description' => '6 stuffed jalapeños',
                'price' => 7.99,
                'sort_order' => 26
            ],
            [
                'name' => 'Mozzarella Sticks (6 pcs)',
                'description' => '6 mozzarella sticks',
                'price' => 7.99,
                'sort_order' => 27
            ]
        ];
        
        foreach ($sides as $side) {
            Product::create([
                'category_id' => $sidesCategory->id,
                'name' => $side['name'],
                'slug' => Str::slug($side['name']),
                'description' => $side['description'],
                'price' => $side['price'],
                'sizes' => null,
                'max_toppings' => 0,
                'is_pizza' => false,
                'is_specialty' => false,
                'has_size_options' => false,
                'has_toppings' => false,
                'active' => true,
                'sort_order' => $side['sort_order'],
            ]);
        }

        // =====================================================================
        // DRINKS
        // =====================================================================
        $drinks = [
            [
                'name' => 'Pop',
                'description' => 'Choose from Coke, Diet Coke, Sprite, or Ginger Ale.',
                'price' => 1.99,
                'sizes' => [
                    'can' => 1.99,
                    '2L bottle' => 3.49,
                    '4-pack' => 4.99
                ],
                'sort_order' => 1
            ],
            [
                'name' => 'Bottled Water',
                'description' => '500ml bottle of spring water.',
                'price' => 1.49,
                'sort_order' => 2
            ]
        ];
        
        foreach ($drinks as $drink) {
            Product::create([
                'category_id' => $drinksCategory->id,
                'name' => $drink['name'],
                'slug' => Str::slug($drink['name']),
                'description' => $drink['description'],
                'price' => $drink['price'],
                'sizes' => $drink['sizes'] ?? null,
                'max_toppings' => 0,
                'is_pizza' => false,
                'is_specialty' => false,
                'has_size_options' => isset($drink['sizes']),
                'has_toppings' => false,
                'active' => true,
                'sort_order' => $drink['sort_order'],
            ]);
        }

        // =====================================================================
        // DAILY SPECIALS
        // =====================================================================
        
        // Monday Special
        Product::create([
            'category_id' => $mondaySpecialCategory->id,
            'name' => 'Monday Madness',
            'slug' => 'monday-madness',
            'description' => 'Medium 2-topping pizza for only $9.99',
            'price' => 9.99,
            'sizes' => null,
            'max_toppings' => 2,
            'is_pizza' => true,
            'is_specialty' => false,
            'has_size_options' => false,
            'has_toppings' => true,
            'active' => true,
            'sort_order' => 1,
        ]);
        
        // Tuesday Special
        Product::create([
            'category_id' => $tuesdaySpecialCategory->id,
            'name' => 'Tuesday Wing Deal',
            'slug' => 'tuesday-wing-deal',
            'description' => '2 lbs of wings for the price of 1 lb',
            'price' => 10.99,
            'sizes' => null,
            'max_toppings' => 0,
            'is_pizza' => false,
            'is_specialty' => false,
            'has_size_options' => false,
            'has_toppings' => false,
            'active' => true,
            'sort_order' => 1,
        ]);
        
        // Wednesday Special
        Product::create([
            'category_id' => $wednesdaySpecialCategory->id,
            'name' => 'Wednesday BOGO',
            'slug' => 'wednesday-bogo',
            'description' => 'Buy one large pizza at regular price, get a second of equal or lesser value for free',
            'price' => 14.99,
            'sizes' => null,
            'max_toppings' => 3,
            'is_pizza' => true,
            'is_specialty' => false,
            'has_size_options' => false,
            'has_toppings' => true,
            'active' => true,
            'sort_order' => 1,
        ]);
        
        // Thursday Special
        Product::create([
            'category_id' => $thursdaySpecialCategory->id,
            'name' => 'Thirsty Thursday',
            'slug' => 'thirsty-thursday',
            'description' => 'XL 3-topping pizza with a free 2L pop',
            'price' => 16.99,
            'sizes' => null,
            'max_toppings' => 3,
            'is_pizza' => true,
            'is_specialty' => false,
            'has_size_options' => false,
            'has_toppings' => true,
            'active' => true,
            'sort_order' => 1,
        ]);
        
        // Weekend Special
        Product::create([
            'category_id' => $weekendSpecialCategory->id,
            'name' => 'Weekend Feast',
            'slug' => 'weekend-feast',
            'description' => '2 Large pizzas with up to 4 toppings each, 2 lbs of wings, garlic bread, and a 2L pop',
            'price' => 44.99,
            'sizes' => null,
            'max_toppings' => 4,
            'is_pizza' => true,
            'is_specialty' => false,
            'has_size_options' => false,
            'has_toppings' => true,
            'active' => true,
            'sort_order' => 1,
        ]);
    }
}
