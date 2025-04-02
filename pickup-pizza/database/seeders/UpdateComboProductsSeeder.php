<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateComboProductsSeeder extends Seeder
{
    public function run(): void
    {
        // Update Two Medium Pizzas Combo
        DB::table('products')
            ->where('name', 'Two Medium Pizzas Combo')
            ->update([
                'description' => '2 Medium Pizzas with 3 Toppings each, 2 lb Chicken Wings, 4 Pops, and Free Garlic Bread',
                'price' => 32.99,
                'sizes' => json_encode([
                    'medium' => ['price' => 32.99, 'wings' => 32], // 2lb wings = 32oz
                ]),
            ]);
            
        DB::table('combos')
            ->where('name', 'Two Medium Pizzas Combo')
            ->update([
                'description' => '2 Medium Pizzas with 3 Toppings each, 2 lb Chicken Wings, 4 Pops, and Free Garlic Bread',
            ]);
            
        // Update Two Large Pizzas Combo
        DB::table('products')
            ->where('name', 'Two Large Pizzas Combo')
            ->update([
                'description' => '2 Large Pizzas with 3 Toppings each, 3 lb Chicken Wings, and Free Dipping Sauce',
                'price' => 39.99,
                'sizes' => json_encode([
                    'large' => ['price' => 39.99, 'wings' => 48], // 3lb wings = 48oz
                ]),
            ]);
            
        DB::table('combos')
            ->where('name', 'Two Large Pizzas Combo')
            ->update([
                'description' => '2 Large Pizzas with 3 Toppings each, 3 lb Chicken Wings, and Free Dipping Sauce',
            ]);
            
        // Update Two XL Pizzas Combo
        DB::table('products')
            ->where('name', 'Two XL Pizzas Combo')
            ->update([
                'description' => '2 XL Pizzas with 3 Toppings each, 3 lb Chicken Wings, and Free Dipping Sauce',
                'price' => 44.99,
                'sizes' => json_encode([
                    'xl' => ['price' => 44.99, 'wings' => 48], // 3lb wings = 48oz
                ]),
            ]);
            
        DB::table('combos')
            ->where('name', 'Two XL Pizzas Combo')
            ->update([
                'description' => '2 XL Pizzas with 3 Toppings each, 3 lb Chicken Wings, and Free Dipping Sauce',
            ]);
            
        // Update Ultimate Pizza & Wings Combo
        DB::table('products')
            ->where('name', 'Ultimate Pizza & Wings Combo')
            ->update([
                'description' => '1 Pizza with Cheese + 3 Toppings, Wings (12 for Medium, 3 lbs for others), Veggie Sticks, Blue Cheese, and Free Dipping Sauce',
                'price' => 27.99,
            ]);
            
        DB::table('combos')
            ->where('name', 'Ultimate Pizza & Wings Combo')
            ->update([
                'description' => '1 Pizza with Cheese + 3 Toppings, Wings (12 for Medium, 3 lbs for others), Veggie Sticks, Blue Cheese, and Free Dipping Sauce',
            ]);
            
        // Delete any duplicate combos (keeping the ones we just updated)
        $uniqueComboNames = ['Two Medium Pizzas Combo', 'Two Large Pizzas Combo', 'Two XL Pizzas Combo', 'Ultimate Pizza & Wings Combo'];
        
        foreach ($uniqueComboNames as $comboName) {
            // Get the first (primary) record for each combo
            $primaryProduct = DB::table('products')
                ->where('name', $comboName)
                ->first();
                
            if ($primaryProduct) {
                // Delete any other products with the same name
                DB::table('products')
                    ->where('name', $comboName)
                    ->where('id', '!=', $primaryProduct->id)
                    ->delete();
                    
                // Do the same for combos table
                $primaryCombo = DB::table('combos')
                    ->where('name', $comboName)
                    ->first();
                    
                if ($primaryCombo) {
                    DB::table('combos')
                        ->where('name', $comboName)
                        ->where('id', '!=', $primaryCombo->id)
                        ->delete();
                }
            }
        }
        
        echo "Updated combo products with correct prices and descriptions\n";
        echo "Removed duplicate combo products\n";
    }
} 