<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Combo;
use App\Models\Topping;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PisaPizzaMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create categories
        $specialtyCategory = $this->getOrCreateCategory('Specialty Pizzas', 'Our chef-crafted specialty pizzas with perfect combinations of premium toppings.', 1);
        $buildYourOwnCategory = $this->getOrCreateCategory('Build Your Own Pizza', 'Create your perfect pizza with your choice of fresh toppings.', 2);
        $twoForOneCategory = $this->getOrCreateCategory('2 For 1 Pizzas', 'Great value 2-for-1 pizza deals with your choice of toppings.', 3);
        $sidesCategory = $this->getOrCreateCategory('Sides', 'Delicious sides to complement your pizza.', 4);
        $drinksCategory = $this->getOrCreateCategory('Drinks', 'Refreshing beverages to complete your meal.', 5);
        $combosCategory = $this->getOrCreateCategory('Combos', 'Value-packed meal deals including pizzas, wings, and more.', 6);
        
        // Get toppings map
        $toppingsMap = $this->getOrCreateToppings();
        
        // =====================================================================
        // SPECIALTY PIZZAS (2 FOR 1)
        // =====================================================================
        $this->seedSpecialtyPizzas($specialtyCategory, $toppingsMap);
        
        // =====================================================================
        // BUILD YOUR OWN / SINGLE PIZZAS
        // =====================================================================
        $this->seedSinglePizzas($buildYourOwnCategory);
        
        // =====================================================================
        // 2 FOR 1 PIZZAS
        // =====================================================================
        $this->seed2For1Pizzas($twoForOneCategory);
        
        // =====================================================================
        // SIDE ORDERS
        // =====================================================================
        $this->seedSideOrders($sidesCategory);
        
        // =====================================================================
        // COMBOS
        // =====================================================================
        $this->seedCombos();
        
        // =====================================================================
        // DIPS & DRINKS
        // =====================================================================
        $this->seedDrinks($drinksCategory);
    }
    
    /**
     * Get or create a category
     */
    private function getOrCreateCategory($name, $description, $sortOrder)
    {
        $category = Category::where('name', $name)->first();
        
        if (!$category) {
            $category = Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $description,
                'sort_order' => $sortOrder,
                'active' => true,
                'is_daily_special' => false,
            ]);
        }
        
        return $category;
    }
    
    /**
     * Get or create toppings
     */
    private function getOrCreateToppings()
    {
        $toppingsMap = [];
        
        $toppingsList = [
            'Pepperoni', 'Italian Sausage', 'Hot Sausage', 'Ham', 'Mushrooms', 
            'Tomatoes', 'Green Peppers', 'Hot Peppers', 'Jalapeños', 'Onions', 
            'Green Olives', 'Anchovies', 'Pineapple', 'Garlic', 'Bacon', 
            'Bacon Bits', 'Black Olives', 'Extra Cheese', 'Real Chicken', 
            'Feta Cheese', 'Sun-dried Tomatoes', 'Ground Beef', 'Meatballs', 'Corn'
        ];
        
        $countsDoubleItems = ['Real Chicken'];
        
        foreach ($toppingsList as $index => $toppingName) {
            $topping = Topping::where('name', $toppingName)->first();
            
            if (!$topping) {
                $topping = Topping::create([
                    'name' => $toppingName,
                    'category' => $this->getToppingCategory($toppingName),
                    'counts_as' => in_array($toppingName, $countsDoubleItems) ? 2 : 1,
                    'price_factor' => 1.0,
                    'display_order' => $index + 1,
                    'is_active' => true,
                ]);
            }
            
            $toppingsMap[$toppingName] = $topping;
        }
        
        return $toppingsMap;
    }
    
    /**
     * Get topping category based on name
     */
    private function getToppingCategory($name)
    {
        $meatToppings = ['Pepperoni', 'Italian Sausage', 'Hot Sausage', 'Ham', 'Bacon', 'Bacon Bits', 'Anchovies', 'Real Chicken', 'Ground Beef', 'Meatballs'];
        $cheeseToppings = ['Extra Cheese', 'Feta Cheese'];
        
        if (in_array($name, $meatToppings)) {
            return 'meat';
        } elseif (in_array($name, $cheeseToppings)) {
            return 'cheese';
        } else {
            return 'veggie';
        }
    }
    
    /**
     * Seed specialty pizzas
     */
    private function seedSpecialtyPizzas($category, $toppingsMap)
    {
        $specialtyPizzas = [
            'Canadian' => [
                'description' => 'Double Pepperoni, Mushrooms, Bacon, Extra Cheese',
                'toppings' => ['Pepperoni', 'Mushrooms', 'Bacon', 'Extra Cheese']
            ],
            'Deluxe' => [
                'description' => 'Pepperoni, Mushrooms, Green Peppers, Onions, Bacon, Tomatoes',
                'toppings' => ['Pepperoni', 'Mushrooms', 'Green Peppers', 'Onions', 'Bacon', 'Tomatoes']
            ],
            'Hawaiian' => [
                'description' => 'Double Ham, Pineapple, Bacon, Extra Cheese',
                'toppings' => ['Ham', 'Pineapple', 'Bacon', 'Extra Cheese']
            ],
            'All Meat' => [
                'description' => 'Pepperoni, Italian Sausage, Bacon, Ham, Ground Beef',
                'toppings' => ['Pepperoni', 'Italian Sausage', 'Bacon', 'Ham', 'Ground Beef']
            ],
            'Mexicana' => [
                'description' => 'Sliced Tomatoes, Ground Beef, Hot Peppers, Onions, Mushrooms',
                'toppings' => ['Tomatoes', 'Ground Beef', 'Hot Peppers', 'Onions', 'Mushrooms']
            ],
            'Vegetarian' => [
                'description' => 'Mushrooms, Green Peppers, Tomatoes, Green Olives, Onions',
                'toppings' => ['Mushrooms', 'Green Peppers', 'Tomatoes', 'Green Olives', 'Onions']
            ],
            'Pisa' => [
                'description' => 'Chicken, Onions, Tomatoes, Mushrooms, Extra Cheese',
                'toppings' => ['Real Chicken', 'Onions', 'Tomatoes', 'Mushrooms', 'Extra Cheese']
            ],
            'Greek' => [
                'description' => 'Mushrooms, Onions, Tomatoes, Feta Cheese, Black Olives',
                'toppings' => ['Mushrooms', 'Onions', 'Tomatoes', 'Feta Cheese', 'Black Olives']
            ],
            'Chicken BBQ' => [
                'description' => 'BBQ Sauce, Chicken, Extra Cheese, Red Onions',
                'toppings' => ['Real Chicken', 'Extra Cheese', 'Onions']
            ],
            'Butter Chicken' => [
                'description' => 'Pizza Butter Chicken Sauce, Butter Chicken, Tomatoes, Red Onions',
                'toppings' => ['Real Chicken', 'Tomatoes', 'Onions']
            ],
            'Mediterranean' => [
                'description' => 'Sun-dried Tomato, Black Olives, Red Onion, Feta Cheese',
                'toppings' => ['Sun-dried Tomatoes', 'Black Olives', 'Onions', 'Feta Cheese']
            ],
            'Meatball Pizza' => [
                'description' => 'Meatballs, Green Peppers, Bacon, Onions',
                'toppings' => ['Meatballs', 'Green Peppers', 'Bacon', 'Onions']
            ],
            'Shawarma Pizza' => [
                'description' => 'Shawarma Sauce, Chicken, Onions, Tomato',
                'toppings' => ['Real Chicken', 'Onions', 'Tomatoes']
            ],
        ];
        
        foreach ($specialtyPizzas as $name => $details) {
            $product = Product::where('name', $name)->where('category_id', $category->id)->first();
            
            if (!$product) {
                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'description' => $details['description'],
                    'price' => 0, // We store the prices in the sizes field for 2-for-1 specialty pizzas
                    'sizes' => [
                        'medium' => 15.49, // Updated price for medium
                        'large' => 16.99,  // Updated price for large
                        'xl' => 19.49,     // Updated price for XL
                    ],
                    'max_toppings' => 0, // No additional toppings allowed for specialty
                    'free_toppings' => count($details['toppings']),
                    'is_pizza' => true,
                    'is_specialty' => true,
                    'has_size_options' => true,
                    'has_toppings' => true, // Allow topping selection for adding extra toppings
                    'has_extras' => false,
                    'active' => true,
                    'sort_order' => 1,
                    'add_ons' => json_encode([
                        'show_extra_toppings_toggle' => true,
                        'extra_topping_price' => [
                            'medium' => 1.60,
                            'large' => 2.10,
                            'xl' => 2.30
                        ]
                    ]),
                ]);
                
                // Attach default toppings
                foreach ($details['toppings'] as $toppingName) {
                    if (isset($toppingsMap[$toppingName])) {
                        $product->toppings()->attach($toppingsMap[$toppingName]->id, [
                            'is_default' => true,
                            'quantity' => 1,
                        ]);
                    }
                }
            }
        }
    }
    
    /**
     * Seed single pizzas
     */
    private function seedSinglePizzas($category)
    {
        $singlePizzas = [
            [
                'name' => 'Medium Pizza',
                'description' => 'Medium pizza with up to 4 toppings',
                'price' => 16.99,
                'max_toppings' => 4,
                'free_toppings' => 4,
                'extra_topping_price' => 1.60,
                'sort_order' => 1,
                'size' => 'medium'
            ],
            [
                'name' => 'Large Pizza',
                'description' => 'Large pizza with up to 4 toppings',
                'price' => 17.99,
                'max_toppings' => 4,
                'free_toppings' => 4,
                'extra_topping_price' => 2.10,
                'sort_order' => 2,
                'size' => 'large'
            ],
            [
                'name' => 'X-Large Pizza',
                'description' => 'X-Large pizza with up to 4 toppings',
                'price' => 18.99,
                'max_toppings' => 4,
                'free_toppings' => 4,
                'extra_topping_price' => 2.30,
                'sort_order' => 3,
                'size' => 'xl'
            ],
            [
                'name' => 'Jumbo Pizza',
                'description' => 'Jumbo pizza with up to 3 toppings',
                'price' => 23.99,
                'max_toppings' => 3,
                'free_toppings' => 3,
                'extra_topping_price' => 2.90,
                'sort_order' => 4,
                'size' => 'jumbo'
            ],
            [
                'name' => 'Slab Pizza',
                'description' => 'Slab pizza with up to 3 toppings',
                'price' => 27.99,
                'max_toppings' => 3,
                'free_toppings' => 3,
                'extra_topping_price' => 2.90,
                'sort_order' => 5,
                'size' => 'slab'
            ],
            [
                'name' => 'Panzerotti',
                'description' => 'Panzerotti with up to 3 toppings',
                'price' => 16.99,
                'max_toppings' => 3,
                'free_toppings' => 3,
                'extra_topping_price' => 1.60,
                'sort_order' => 6,
                'size' => 'panzerotti'
            ],
        ];
        
        foreach ($singlePizzas as $pizza) {
            $product = Product::where('name', $pizza['name'])->where('category_id', $category->id)->first();
            
            if (!$product) {
                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => $pizza['name'],
                    'slug' => Str::slug($pizza['name']),
                    'description' => $pizza['description'],
                    'price' => $pizza['price'],
                    'max_toppings' => $pizza['max_toppings'],
                    'free_toppings' => $pizza['free_toppings'],
                    'is_pizza' => true,
                    'is_specialty' => false,
                    'has_size_options' => false,
                    'has_toppings' => true,
                    'has_extras' => true,
                    'active' => true,
                    'sort_order' => $pizza['sort_order'],
                    'add_ons' => json_encode([
                        'extra_topping_price' => $pizza['extra_topping_price'],
                        'show_extra_toppings_toggle' => true
                    ])
                ]);
            }
        }
    }
    
    /**
     * Create an extra topping option for a pizza product
     */
    private function createExtraToppingExtra($product, $price)
    {
        // No longer needed - using topping toggle approach instead
    }
    
    /**
     * Seed 2 for 1 pizzas
     */
    private function seed2For1Pizzas($category)
    {
        $twoForOnePizzas = [
            [
                'name' => '2 For 1 Medium Pizzas',
                'description' => 'Two medium pizzas with up to 3 toppings each',
                'price' => 26.99,
                'max_toppings' => 3,
                'free_toppings' => 3,
                'extra_topping_price' => 1.60,
                'add_third_price' => 10.99,
                'sort_order' => 1,
                'size' => 'medium'
            ],
            [
                'name' => '2 For 1 Large Pizzas',
                'description' => 'Two large pizzas with up to 3 toppings each',
                'price' => 29.99,
                'max_toppings' => 3,
                'free_toppings' => 3,
                'extra_topping_price' => 2.10,
                'add_third_price' => 12.99,
                'sort_order' => 2,
                'size' => 'large'
            ],
            [
                'name' => '2 For 1 X-Large Pizzas',
                'description' => 'Two X-Large pizzas with up to 3 toppings each',
                'price' => 32.99,
                'max_toppings' => 3,
                'free_toppings' => 3,
                'extra_topping_price' => 2.30,
                'add_third_price' => 13.99,
                'sort_order' => 3,
                'size' => 'xl'
            ],
            [
                'name' => '2 For 1 Jumbo Pizzas',
                'description' => 'Two jumbo pizzas with up to 3 toppings each',
                'price' => 40.99,
                'max_toppings' => 3,
                'free_toppings' => 3,
                'extra_topping_price' => 2.90,
                'add_third_price' => null,
                'sort_order' => 4,
                'size' => 'jumbo'
            ],
            [
                'name' => '2 For 1 Slab Pizzas',
                'description' => 'Two slab pizzas with up to 3 toppings each',
                'price' => 49.99,
                'max_toppings' => 3,
                'free_toppings' => 3,
                'extra_topping_price' => 2.90,
                'add_third_price' => 24.99,
                'sort_order' => 5,
                'size' => 'slab'
            ],
        ];
        
        foreach ($twoForOnePizzas as $pizza) {
            $product = Product::where('name', $pizza['name'])->where('category_id', $category->id)->first();
            
            if (!$product) {
                $addOns = [
                    'extra_topping_price' => $pizza['extra_topping_price'],
                    'show_extra_toppings_toggle' => true,
                    'separate_toppings_for_pizzas' => true,
                    'num_pizzas' => 2
                ];
                
                if ($pizza['add_third_price']) {
                    $addOns['add_third_pizza'] = [
                        'name' => 'Add 3rd Pizza',
                        'price' => $pizza['add_third_price']
                    ];
                    $addOns['max_pizzas'] = 3;
                }
                
                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => $pizza['name'],
                    'slug' => Str::slug($pizza['name']),
                    'description' => $pizza['description'],
                    'price' => $pizza['price'],
                    'max_toppings' => $pizza['max_toppings'],
                    'free_toppings' => $pizza['free_toppings'],
                    'is_pizza' => true,
                    'is_specialty' => false,
                    'has_size_options' => false,
                    'has_toppings' => true,
                    'has_extras' => true,
                    'active' => true,
                    'sort_order' => $pizza['sort_order'],
                    'add_ons' => json_encode($addOns)
                ]);
            }
        }
    }
    
    /**
     * Create extra topping options for 2-for-1 pizzas
     */
    private function create2For1ExtraToppingExtras($product, $price)
    {
        // No longer needed - using topping toggle approach instead
    }
    
    /**
     * Seed side orders
     */
    private function seedSideOrders($category)
    {
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
        
        // Create wing flavor extras
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
        
        // Create dipping sauce extras
        $dippingSauces = [
            ['name' => 'Ranch', 'price' => 0],
            ['name' => 'Blue Cheese', 'price' => 0],
            ['name' => 'Marinara', 'price' => 0],
            ['name' => 'Garlic', 'price' => 0],
            ['name' => 'BBQ', 'price' => 0],
            ['name' => 'Plum', 'price' => 0]
        ];
        
        foreach ($sideOrders as $side) {
            $product = Product::where('name', $side['name'])->where('category_id', $category->id)->first();
            
            if (!$product) {
                $product = Product::create([
                    'category_id' => $category->id,
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
                
                // Add dipping sauce options to dipping sauce product
                if ($side['name'] === 'Dipping Sauce') {
                    foreach ($dippingSauces as $index => $sauce) {
                        $extra = \App\Models\ProductExtra::create([
                            'name' => $sauce['name'],
                            'price' => $sauce['price'],
                            'description' => '',
                            'is_default' => false,
                            'max_quantity' => 5,
                            'active' => true,
                        ]);
                        
                        // Attach the extra to the product using the pivot table
                        $product->extras()->attach($extra->id);
                    }
                }
            }
        }
    }
    
    /**
     * Seed drinks
     */
    private function seedDrinks($category)
    {
        $drinks = [
            [
                'name' => '4 Pops',
                'description' => 'Four 355ml pop cans',
                'price' => 4.99,
                'sort_order' => 1
            ],
            [
                'name' => '2L Pop',
                'description' => '2L bottle of pop',
                'price' => 3.49,
                'sort_order' => 2
            ],
        ];
        
        foreach ($drinks as $drink) {
            $product = Product::where('name', $drink['name'])->where('category_id', $category->id)->first();
            
            if (!$product) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $drink['name'],
                    'slug' => Str::slug($drink['name']),
                    'description' => $drink['description'],
                    'price' => $drink['price'],
                    'is_pizza' => false,
                    'is_specialty' => false,
                    'has_size_options' => false,
                    'has_toppings' => false,
                    'has_extras' => false,
                    'active' => true,
                    'sort_order' => $drink['sort_order'],
                ]);
            }
        }
    }
    
    /**
     * Seed Combos
     */
    private function seedCombos(): void
    {
        $combos = [
            'Pizza & Wings Combo' => [
                'price' => 29.99,
                'description' => 'Large 3-topping pizza with 1 lb of wings',
                'products' => ['Large Pizza', '1 LB Wings']
            ],
            'Family Feast' => [
                'price' => 39.99,
                'description' => 'Large 3-topping pizza, 2 lb wings, and garlic bread',
                'products' => ['Large Pizza', '2 LB Wings', 'Garlic Bread']
            ],
            'Party Pack' => [
                'price' => 49.99,
                'description' => '2 Large 3-topping pizzas, 2 lb wings, and 2L pop',
                'products' => ['Large Pizza', 'Large Pizza', '2 LB Wings', '2L Pop']
            ],
            'Student Special' => [
                'price' => 24.99,
                'description' => 'Medium 2-topping pizza, garlic bread, and 2L pop',
                'products' => ['Medium Pizza', 'Garlic Bread', '2L Pop']
            ],
            'Wing Lovers' => [
                'price' => 34.99,
                'description' => '3 lb wings with fries and 2L pop',
                'products' => ['3 LB Wings', 'Fries', '2L Pop']
            ]
        ];

        $comboCategory = Category::where('name', 'Combos')->first();

        foreach ($combos as $name => $details) {
            $combo = Product::create([
                'name' => $name,
                'price' => $details['price'],
                'category_id' => $comboCategory->id,
                'description' => $details['description'],
                'active' => true,
                'is_combo' => true,
                'slug' => Str::slug($name)
            ]);

            // Create the combo relationship
            $comboModel = Combo::create([
                'product_id' => $combo->id,
                'name' => $name,
                'description' => $details['description'],
                'slug' => Str::slug($name)
            ]);

            // Add products to the combo
            foreach ($details['products'] as $productName) {
                $product = Product::where('name', $productName)->first();
                if ($product) {
                    $comboModel->products()->attach($product->id);
                }
            }
        }
    }
}
