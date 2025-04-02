<?php

namespace Database\Seeders;

use App\Models\Combo;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixCombosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, clear any existing relationships
        DB::table('combo_product')->truncate();
        
        // Get the combo data
        $combos = Combo::all();
        
        // For each combo, add the appropriate products
        foreach ($combos as $combo) {
            if (str_contains($combo->name, 'Ultimate Medium')) {
                $this->addUltimatePizzaWingsCombo($combo, 'medium', 27.99, 12);
            } elseif (str_contains($combo->name, 'Ultimate Large')) {
                $this->addUltimatePizzaWingsCombo($combo, 'large', 38.99, 3);
            } elseif (str_contains($combo->name, 'Ultimate X-Large')) {
                $this->addUltimatePizzaWingsCombo($combo, 'xl', 40.99, 3);
            } elseif (str_contains($combo->name, 'Ultimate Jumbo')) {
                $this->addUltimatePizzaWingsCombo($combo, 'jumbo', 44.99, 3);
            } elseif (str_contains($combo->name, 'Ultimate Slab')) {
                $this->addUltimatePizzaWingsCombo($combo, 'slab', 50.99, 3);
            } else {
                // For other combos, add at least a placeholder product
                $pizza = Product::where('name', 'like', '%Medium Pizza%')->first();
                if ($pizza) {
                    $combo->products()->attach($pizza->id, [
                        'quantity' => 1,
                        'name' => 'Combo Pizza',
                        'is_pizza' => true,
                        'max_toppings' => 3
                    ]);
                }
                
                $wings = Product::where('name', 'like', '%1 LB Wings%')->first();
                if ($wings) {
                    $combo->products()->attach($wings->id, [
                        'quantity' => 1,
                        'name' => 'Combo Wings',
                        'is_pizza' => false
                    ]);
                }
            }
            
            $this->command->info("Fixed products for combo: {$combo->name}");
        }
        
        $this->command->info('Combos fixed successfully');
    }
    
    /**
     * Add products for an Ultimate Pizza & Wings Combo
     */
    private function addUltimatePizzaWingsCombo($combo, $size, $price, $wingsCount)
    {
        // Find the appropriate pizza product based on size
        $pizzaName = "Single " . ucfirst($size) . " Pizza";
        $pizza = Product::where('name', $pizzaName)->first();
        if (!$pizza) {
            $pizza = Product::where('is_pizza', true)->first(); // Fallback
        }
        
        // Find products for wings, veggie sticks, and blue cheese
        $wings = Product::where('name', 'like', '%1 LB Wings%')->first();
        $veggieSticks = Product::where('name', 'like', '%Veggie Sticks%')->first();
        $blueCheese = Product::where('name', 'like', '%Blue Cheese%')->first();
        
        // Add the pizza with 3 toppings
        if ($pizza) {
            $combo->products()->attach($pizza->id, [
                'quantity' => 1,
                'size' => $size,
                'max_toppings' => 3,
                'name' => ucfirst($size) . ' Pizza with 3 Toppings',
                'is_pizza' => true
            ]);
        }
        
        // Add wings with quantity (12 or 3 lbs)
        if ($wings) {
            $wingsQuantity = ($wingsCount === 12) ? 1 : 3; // 1 or 3 pounds
            $combo->products()->attach($wings->id, [
                'quantity' => $wingsQuantity,
                'name' => $wingsCount . ' Wings',
                'is_pizza' => false
            ]);
        }
        
        // Add veggie sticks
        if ($veggieSticks) {
            $combo->products()->attach($veggieSticks->id, [
                'quantity' => 1,
                'name' => 'Veggie Sticks',
                'is_pizza' => false
            ]);
        }
        
        // Add blue cheese dip
        if ($blueCheese) {
            $combo->products()->attach($blueCheese->id, [
                'quantity' => 1,
                'name' => 'Blue Cheese Dip',
                'is_pizza' => false
            ]);
        }
    }
} 