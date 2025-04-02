<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductExtra;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UpdateSinglePizzaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Rename the category from "Build Your Own Pizza" to "Single Pizza"
        $category = Category::where('name', 'Build Your Own Pizza')->first();
        if ($category) {
            $category->update([
                'name' => 'Single Pizza',
                'slug' => 'single-pizza',
                'description' => 'Individual pizzas with your choice of toppings.'
            ]);
            
            $this->command->info('Category renamed to "Single Pizza"');
        } else {
            $this->command->error('Build Your Own Pizza category not found!');
            return;
        }
        
        // 2. Disable all products in this category first
        Product::where('category_id', $category->id)->update(['active' => false]);
        $this->command->info('All products in Single Pizza category disabled');
        
        // 3. Define single pizza products according to the menu
        $singlePizzas = [
            [
                'id' => 38, // Medium Pizza
                'name' => 'Single Medium Pizza',
                'description' => 'Medium pizza with up to 4 toppings included',
                'price' => 16.99,
                'max_toppings' => 4,
                'free_toppings' => 4,
            ],
            [
                'id' => 39, // Large Pizza
                'name' => 'Single Large Pizza',
                'description' => 'Large pizza with up to 4 toppings included',
                'price' => 17.99,
                'max_toppings' => 4,
                'free_toppings' => 4,
            ],
            [
                'id' => 40, // X-Large Pizza
                'name' => 'Single X-Large Pizza',
                'description' => 'X-Large pizza with up to 4 toppings included',
                'price' => 18.99,
                'max_toppings' => 4,
                'free_toppings' => 4,
            ],
            [
                'id' => 41, // Jumbo Pizza
                'name' => 'Single Jumbo Pizza',
                'description' => 'Jumbo pizza with up to 3 toppings included',
                'price' => 23.99,
                'max_toppings' => 3,
                'free_toppings' => 3,
            ],
            [
                'id' => 42, // Slab Pizza
                'name' => 'Single Slab Pizza',
                'description' => 'Slab pizza with up to 3 toppings included',
                'price' => 27.99,
                'max_toppings' => 3,
                'free_toppings' => 3,
            ],
            [
                'id' => 43, // Panzerotti
                'name' => 'Single Panzerotti',
                'description' => 'Panzerotti with up to 3 toppings included',
                'price' => 16.99,
                'max_toppings' => 3,
                'free_toppings' => 3,
            ]
        ];
        
        // 4. Create or update the single pizza products
        foreach ($singlePizzas as $pizzaData) {
            $product = Product::find($pizzaData['id']);
            
            if ($product) {
                // Update existing product
                $product->update([
                    'name' => $pizzaData['name'],
                    'slug' => Str::slug($pizzaData['name']),
                    'description' => $pizzaData['description'],
                    'price' => $pizzaData['price'],
                    'max_toppings' => $pizzaData['max_toppings'],
                    'free_toppings' => $pizzaData['free_toppings'],
                    'is_pizza' => true,
                    'is_specialty' => false,
                    'has_size_options' => false,
                    'has_toppings' => true,
                    'has_extras' => true,
                    'active' => true,
                ]);
                
                $this->command->info("Updated: {$pizzaData['name']}");
            } else {
                // Create new product if not found
                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => $pizzaData['name'],
                    'slug' => Str::slug($pizzaData['name']),
                    'description' => $pizzaData['description'],
                    'price' => $pizzaData['price'],
                    'max_toppings' => $pizzaData['max_toppings'],
                    'free_toppings' => $pizzaData['free_toppings'],
                    'is_pizza' => true,
                    'is_specialty' => false,
                    'has_size_options' => false,
                    'has_toppings' => true,
                    'has_extras' => true,
                    'active' => true,
                ]);
                
                $this->command->info("Created: {$pizzaData['name']}");
            }
            
            // 5. Add the "4 Pops" and "1 LB Wings" upsell options to each single pizza product
            
            // Remove any existing extras first
            $product->extras()->detach();
            
            // Create "Add 4 Pops" option
            $popsExtra = ProductExtra::firstOrCreate(
                ['name' => 'Add 4 Pops'],
                [
                    'description' => 'Add 4 cans of pop to your order',
                    'price' => 4.99,
                    'max_quantity' => 1,
                    'is_default' => false,
                    'active' => true,
                ]
            );
            
            // Create "Add 1 LB Wings" option
            $wingsExtra = ProductExtra::firstOrCreate(
                ['name' => 'Add 1 LB Wings'],
                [
                    'description' => 'Add 1 pound of wings to your order',
                    'price' => 10.49,
                    'max_quantity' => 3,
                    'is_default' => false,
                    'active' => true,
                ]
            );
            
            // Attach extras to the product
            $product->extras()->attach($popsExtra->id);
            $product->extras()->attach($wingsExtra->id);
            
            $this->command->info("Added upsell options to {$pizzaData['name']}");
        }
        
        $this->command->info('Single Pizza category update completed');
    }
} 