<?php

namespace Database\Seeders;

use App\Models\Topping;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ToppingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Meat toppings
        $meatToppings = [
            ['name' => 'Pepperoni', 'counts_double' => false, 'sort_order' => 1],
            ['name' => 'Italian Sausage', 'counts_double' => false, 'sort_order' => 2],
            ['name' => 'Bacon', 'counts_double' => false, 'sort_order' => 3],
            ['name' => 'Ham', 'counts_double' => false, 'sort_order' => 4],
            ['name' => 'Ground Beef', 'counts_double' => false, 'sort_order' => 5],
            ['name' => 'Real Chicken', 'counts_double' => true, 'sort_order' => 6], // Counts as 2 toppings
            ['name' => 'Salami', 'counts_double' => false, 'sort_order' => 7],
            ['name' => 'Anchovies', 'counts_double' => false, 'sort_order' => 8],
        ];
        
        // Veggie toppings
        $veggieToppings = [
            ['name' => 'Mushrooms', 'counts_double' => false, 'sort_order' => 10],
            ['name' => 'Onions', 'counts_double' => false, 'sort_order' => 11],
            ['name' => 'Green Peppers', 'counts_double' => false, 'sort_order' => 12],
            ['name' => 'Black Olives', 'counts_double' => false, 'sort_order' => 13],
            ['name' => 'Tomatoes', 'counts_double' => false, 'sort_order' => 14],
            ['name' => 'Pineapple', 'counts_double' => false, 'sort_order' => 15],
            ['name' => 'Jalapeños', 'counts_double' => false, 'sort_order' => 16],
            ['name' => 'Spinach', 'counts_double' => false, 'sort_order' => 17],
            ['name' => 'Banana Peppers', 'counts_double' => false, 'sort_order' => 18],
            ['name' => 'Roasted Red Peppers', 'counts_double' => false, 'sort_order' => 19],
            ['name' => 'Sun-Dried Tomatoes', 'counts_double' => false, 'sort_order' => 20],
        ];
        
        // Cheese toppings
        $cheeseToppings = [
            ['name' => 'Extra Cheese', 'counts_double' => false, 'sort_order' => 30],
            ['name' => 'Feta Cheese', 'counts_double' => false, 'sort_order' => 31],
            ['name' => 'Mozzarella', 'counts_double' => false, 'sort_order' => 32],
            ['name' => 'Parmesan', 'counts_double' => false, 'sort_order' => 33],
            ['name' => 'Cheddar', 'counts_double' => false, 'sort_order' => 34],
        ];
        
        // Create toppings
        foreach ($meatToppings as $topping) {
            Topping::create([
                'name' => $topping['name'],
                'category' => 'meat',
                'counts_as' => $topping['counts_double'] ? 2 : 1,
                'price_factor' => 1.0,
                'display_order' => $topping['sort_order'],
                'is_active' => true,
            ]);
        }
        
        foreach ($veggieToppings as $topping) {
            Topping::create([
                'name' => $topping['name'],
                'category' => 'veggie',
                'counts_as' => $topping['counts_double'] ? 2 : 1,
                'price_factor' => 1.0,
                'display_order' => $topping['sort_order'],
                'is_active' => true,
            ]);
        }
        
        foreach ($cheeseToppings as $topping) {
            Topping::create([
                'name' => $topping['name'],
                'category' => 'cheese',
                'counts_as' => $topping['counts_double'] ? 2 : 1,
                'price_factor' => 1.0,
                'display_order' => $topping['sort_order'],
                'is_active' => true,
            ]);
        }
    }
}
