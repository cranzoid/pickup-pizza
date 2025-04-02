<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Topping;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpecialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Find or create the "Today's Specials" category
            $category = Category::firstOrCreate(
                ['slug' => 'todays-specials'],
                [
                    'name' => 'Today\'s Specials',
                    'description' => 'Special deals available today',
                    'sort_order' => 12, // After weekend specials
                    'active' => true,
                    'is_daily_special' => false
                ]
            );
            
            echo "Category ID: " . $category->id . PHP_EOL;
            
            // Create WALKIN Specials (always available)
            $this->createWalkinSpecials($category);
            
            // Create DAY Specials (day-specific)
            $this->createDaySpecials();
            
            echo "Seeding completed successfully!" . PHP_EOL;
        } catch (\Exception $e) {
            echo "Error in SpecialsSeeder: " . $e->getMessage() . PHP_EOL;
            echo "File: " . $e->getFile() . " on line " . $e->getLine() . PHP_EOL;
        }
    }
    
    /**
     * Create the WALKIN Specials (everyday specials)
     */
    private function createWalkinSpecials($category)
    {
        // Get pepperoni topping ID for pre-selected items
        $pepperoniTopping = Topping::where('name', 'Pepperoni')->first();
        
        $walkinSpecials = [
            [
                'name' => '1 X-Large Pizza (1 Topping)',
                'description' => 'One X-Large pizza with 1 topping of your choice',
                'price' => 12.99,
                'max_toppings' => 1,
                'is_pizza' => true,
                'has_toppings' => true,
                'is_customizable' => true
            ],
            [
                'name' => '1 Medium Pizza (5 Toppings)',
                'description' => 'One Medium pizza with up to 5 toppings of your choice',
                'price' => 12.99,
                'max_toppings' => 5,
                'is_pizza' => true,
                'has_toppings' => true,
                'is_customizable' => true
            ],
            [
                'name' => '1 X-Large Pizza (3 Toppings)',
                'description' => 'One X-Large pizza with up to 3 toppings of your choice',
                'price' => 15.99,
                'max_toppings' => 3,
                'is_pizza' => true,
                'has_toppings' => true,
                'is_customizable' => true
            ],
            [
                'name' => '2 Large Pizzas (6 Toppings Combined)',
                'description' => 'Two Large pizzas with up to 3 toppings each (6 toppings combined)',
                'price' => 27.99,
                'max_toppings' => 3, // 3 per pizza, handled in view
                'is_pizza' => true,
                'has_toppings' => true,
                'is_customizable' => true
            ],
            [
                'name' => '1 lb Chicken Wings',
                'description' => 'Delicious chicken wings (1 lb)',
                'price' => 10.49,
                'is_pizza' => false,
                'is_customizable' => false
            ],
            [
                'name' => '1 Large Pizza & 1 lb Wings & 3 Pops',
                'description' => 'One Large pizza with up to 3 toppings, 1 pound of wings, and 3 pops',
                'price' => 25.99,
                'max_toppings' => 3,
                'is_pizza' => true,
                'has_toppings' => true,
                'is_customizable' => true
            ]
        ];
        
        foreach ($walkinSpecials as $special) {
            $slug = Str::slug($special['name']);
            
            // Check if the product already exists
            $existingProduct = Product::where('slug', $slug)->first();
            if ($existingProduct) {
                echo "Product with slug '{$slug}' already exists. Skipping.\n";
                continue;
            }
            
            try {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $special['name'],
                    'slug' => $slug,
                    'description' => $special['description'],
                    'price' => $special['price'],
                    'max_toppings' => $special['max_toppings'] ?? null,
                    'is_pizza' => $special['is_pizza'] ?? false,
                    'has_toppings' => $special['has_toppings'] ?? false,
                    'is_customizable' => $special['is_customizable'] ?? true,
                    'active' => true,
                    'sort_order' => 0
                ]);
                echo "Created walkin special: {$special['name']}\n";
            } catch (\Exception $e) {
                echo "Error creating product '{$special['name']}': {$e->getMessage()}\n";
            }
        }
    }
    
    /**
     * Create the DAY Specials (specific days of the week)
     */
    private function createDaySpecials()
    {
        // Get the pepperoni topping for Wednesday special
        $pepperoniTopping = Topping::where('name', 'Pepperoni')->first();
        
        $daySpecials = [
            [
                'name' => 'Monday Special - Large Pizza (3 Toppings)',
                'description' => 'One Large pizza with up to 3 toppings of your choice',
                'price' => 11.49,
                'max_toppings' => 3,
                'is_pizza' => true,
                'has_toppings' => true,
                'display_day' => 'Monday',
                'is_customizable' => true
            ],
            [
                'name' => 'Tuesday Special - 2 Medium Pizzas (1 Topping Each)',
                'description' => 'Two Medium pizzas with 1 topping each',
                'price' => 13.49,
                'max_toppings' => 1,
                'is_pizza' => true,
                'has_toppings' => true,
                'display_day' => 'Tuesday',
                'is_customizable' => true
            ],
            [
                'name' => 'Wednesday Special - Medium Pepperoni Pizza & 1 lb Wings',
                'description' => 'One Medium Pepperoni pizza and 1 pound of wings',
                'price' => 15.99,
                'max_toppings' => 1,
                'is_pizza' => true,
                'has_toppings' => true,
                'display_day' => 'Wednesday',
                'is_customizable' => true,
                'preselected_toppings' => $pepperoniTopping ? json_encode([$pepperoniTopping->id]) : null
            ],
            [
                'name' => 'Thursday Special - Medium Nachos',
                'description' => 'Medium Nachos with salsa',
                'price' => 11.49,
                'is_pizza' => false,
                'display_day' => 'Thursday',
                'is_customizable' => false
            ],
            [
                'name' => 'Friday Special - Pizza Sub (3 Toppings)',
                'description' => 'Pizza Sub with up to 3 toppings of your choice',
                'price' => 8.99,
                'max_toppings' => 3,
                'is_pizza' => false,
                'has_toppings' => true,
                'display_day' => 'Friday',
                'is_customizable' => true
            ],
            [
                'name' => 'Saturday Special - 2 lbs Chicken Wings',
                'description' => 'Delicious chicken wings (2 lbs)',
                'price' => 17.49,
                'is_pizza' => false,
                'display_day' => 'Saturday',
                'is_customizable' => false
            ],
            [
                'name' => 'Sunday Special - Shawarma Poutine',
                'description' => 'Delicious Shawarma Poutine',
                'price' => 12.99,
                'is_pizza' => false,
                'display_day' => 'Sunday',
                'is_customizable' => false
            ]
        ];
        
        // Find the original categories for day specials
        $mondayCategory = Category::where('day_of_week', 'monday')->first();
        $tuesdayCategory = Category::where('day_of_week', 'tuesday')->first();
        $wednesdayCategory = Category::where('day_of_week', 'wednesday')->first();
        $thursdayCategory = Category::where('day_of_week', 'thursday')->first();
        $weekendCategory = Category::where('day_of_week', 'weekend')->first();
        
        foreach ($daySpecials as $special) {
            // Determine the appropriate category
            $categoryId = null;
            
            switch ($special['display_day']) {
                case 'Monday':
                    $categoryId = $mondayCategory ? $mondayCategory->id : null;
                    break;
                case 'Tuesday':
                    $categoryId = $tuesdayCategory ? $tuesdayCategory->id : null;
                    break;
                case 'Wednesday':
                    $categoryId = $wednesdayCategory ? $wednesdayCategory->id : null;
                    break;
                case 'Thursday':
                    $categoryId = $thursdayCategory ? $thursdayCategory->id : null;
                    break;
                case 'Friday':
                case 'Saturday':
                case 'Sunday':
                    $categoryId = $weekendCategory ? $weekendCategory->id : null;
                    break;
            }
            
            if ($categoryId) {
                $slug = Str::slug($special['name']);
                
                // Check if the product already exists
                $existingProduct = Product::where('slug', $slug)->first();
                if ($existingProduct) {
                    echo "Product with slug '{$slug}' already exists. Skipping.\n";
                    continue;
                }
                
                try {
                    Product::create([
                        'category_id' => $categoryId,
                        'name' => $special['name'],
                        'slug' => $slug,
                        'description' => $special['description'],
                        'price' => $special['price'],
                        'max_toppings' => $special['max_toppings'] ?? null,
                        'is_pizza' => $special['is_pizza'] ?? false,
                        'has_toppings' => $special['has_toppings'] ?? false,
                        'display_day' => $special['display_day'],
                        'preselected_toppings' => $special['preselected_toppings'] ?? null,
                        'is_customizable' => $special['is_customizable'] ?? true,
                        'active' => true,
                        'sort_order' => 0
                    ]);
                    echo "Created day special: {$special['name']}\n";
                } catch (\Exception $e) {
                    echo "Error creating product '{$special['name']}': {$e->getMessage()}\n";
                }
            } else {
                echo "Could not find category for day: {$special['display_day']}\n";
            }
        }
    }
}