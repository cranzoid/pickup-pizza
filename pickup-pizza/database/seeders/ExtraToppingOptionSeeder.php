<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductExtra;
use App\Models\Setting;

class ExtraToppingOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure the settings exist
        $this->ensureSettingsExist();
        
        // Update products to enable extra toppings but remove previous extras
        $this->updatePizzaProducts();
    }
    
    /**
     * Ensure the pizza settings exist
     */
    private function ensureSettingsExist()
    {
        // Basic extra topping toggle
        if (!Setting::where('key', 'enable_extra_toppings')->exists()) {
            Setting::create([
                'key' => 'enable_extra_toppings',
                'value' => '1',
                'group' => 'pizza',
                'type' => 'boolean',
                'is_public' => true,
            ]);
        }
        
        // Separate topping selection for each pizza in 2-for-1 deals
        if (!Setting::where('key', 'separate_pizza_toppings')->exists()) {
            Setting::create([
                'key' => 'separate_pizza_toppings',
                'value' => '1',
                'group' => 'pizza',
                'type' => 'boolean',
                'is_public' => true,
            ]);
        }
        
        // Button color for extra topping toggle
        if (!Setting::where('key', 'extra_toppings_button_color')->exists()) {
            Setting::create([
                'key' => 'extra_toppings_button_color',
                'value' => 'red',
                'group' => 'pizza',
                'type' => 'string',
                'is_public' => true,
            ]);
        }
        
        // Use the same topping list for extras
        if (!Setting::where('key', 'use_same_topping_list_for_extras')->exists()) {
            Setting::create([
                'key' => 'use_same_topping_list_for_extras',
                'value' => '1',
                'group' => 'pizza',
                'type' => 'boolean',
                'is_public' => true,
            ]);
        }
    }
    
    /**
     * Update pizza products to support the extra topping toggle approach
     */
    private function updatePizzaProducts()
    {
        // Get all pizza products that aren't specialty
        $pizzaProducts = Product::where('is_pizza', true)
            ->where('is_specialty', false)
            ->get();
        
        foreach ($pizzaProducts as $pizza) {
            // Remove any previously created "Extra Topping" product extras
            $this->removeExtraToppingExtras($pizza);
            
            // Update pizza settings to support the extra topping toggle
            $this->updatePizzaForExtraToppingToggle($pizza);
        }
    }
    
    /**
     * Remove any "Extra Topping" product extras from a pizza
     */
    private function removeExtraToppingExtras($pizza)
    {
        // Find any extras with "Extra Topping" in the name
        $extraToppingExtras = $pizza->extras()
            ->where(function($query) {
                $query->where('name', 'Extra Topping')
                    ->orWhere('name', 'Extra Topping (First Pizza)')
                    ->orWhere('name', 'Extra Topping (Second Pizza)');
            })
            ->get();
            
        if ($extraToppingExtras->count() > 0) {
            // Detach the extras from the product
            foreach ($extraToppingExtras as $extra) {
                $pizza->extras()->detach($extra->id);
                
                // Check if any other products are using this extra
                $otherProducts = $extra->products()->count();
                
                // If no other products are using it, delete it
                if ($otherProducts === 0) {
                    $extra->delete();
                }
            }
        }
    }
    
    /**
     * Update a pizza product to support the extra topping toggle
     */
    private function updatePizzaForExtraToppingToggle($pizza)
    {
        // Ensure the pizza has extras enabled
        $pizza->has_extras = true;
        
        // Get/Update the add_ons JSON
        $addOns = json_decode($pizza->add_ons, true) ?: [];
        
        // Ensure extra_topping_price is set
        if (!isset($addOns['extra_topping_price'])) {
            $addOns['extra_topping_price'] = $this->getExtraToppingPrice($pizza);
        }
        
        // Add flag to show extra toppings toggle
        $addOns['show_extra_toppings_toggle'] = true;
        
        // For 2-for-1 pizzas, add separate topping flags
        if (strpos($pizza->name, '2 For 1') !== false) {
            $addOns['separate_toppings_for_pizzas'] = true;
            $addOns['num_pizzas'] = 2;
            
            // If there's an "Add 3rd Pizza" option, note it
            if (isset($addOns['add_third_pizza'])) {
                $addOns['max_pizzas'] = 3;
            }
        }
        
        // Save the updated add_ons
        $pizza->add_ons = json_encode($addOns);
        $pizza->save();
    }
    
    /**
     * Get the extra topping price for a product
     */
    private function getExtraToppingPrice($product)
    {
        // Try to get from add_ons field first
        $addOns = json_decode($product->add_ons, true);
        if ($addOns && isset($addOns['extra_topping_price'])) {
            return $addOns['extra_topping_price'];
        }
        
        // Default pricing based on size if available in the product name
        if (strpos($product->name, 'Medium') !== false) {
            return 1.60;
        } elseif (strpos($product->name, 'Large') !== false) {
            return 2.10;
        } elseif (strpos($product->name, 'X-Large') !== false) {
            return 2.30;
        } elseif (strpos($product->name, 'Jumbo') !== false) {
            return 2.90;
        } elseif (strpos($product->name, 'Slab') !== false) {
            return 2.90;
        } else {
            return 1.60; // Default to medium price
        }
    }
}
