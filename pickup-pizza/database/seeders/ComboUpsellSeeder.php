<?php

namespace Database\Seeders;

use App\Models\Combo;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComboUpsellSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, clear any existing upsell relationships
        DB::table('combo_upsell_product')->truncate();
        
        // Get common upsell products
        $pop = Product::where('name', 'Pop')->first();
        $wings = Product::where('name', 'like', '%1 LB Wings%')->first();
        $garlicBread = Product::where('name', 'like', '%Garlic Bread%')->first();
        $dips = Product::where('category_id', function($query) {
            $query->select('id')->from('categories')->where('name', 'like', '%Sides%');
        })->where('name', 'like', '%Dip%')->get();
        
        // Get all combos
        $combos = Combo::all();
        
        foreach ($combos as $combo) {
            // Add pop as an upsell to all combos
            if ($pop) {
                DB::table('combo_upsell_product')->insert([
                    'combo_id' => $combo->id,
                    'product_id' => $pop->id,
                    'custom_name' => 'Add Pop',
                    'custom_description' => 'Add a 2L bottle of pop to your order',
                    'sort_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->command->info("Added Pop upsell to: {$combo->name}");
            }
            
            // For pizza combos that don't already have wings, add wings as an upsell
            if ($wings && !str_contains(strtolower($combo->name), 'wing')) {
                DB::table('combo_upsell_product')->insert([
                    'combo_id' => $combo->id,
                    'product_id' => $wings->id,
                    'custom_name' => 'Add Wings',
                    'custom_description' => 'Add 1 LB of wings to your order',
                    'sort_order' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->command->info("Added Wings upsell to: {$combo->name}");
            }
            
            // Add garlic bread as an upsell option
            if ($garlicBread) {
                DB::table('combo_upsell_product')->insert([
                    'combo_id' => $combo->id,
                    'product_id' => $garlicBread->id,
                    'sort_order' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->command->info("Added Garlic Bread upsell to: {$combo->name}");
            }
            
            // Add dips as upsells (max 2 random dips per combo)
            if ($dips->count() > 0) {
                $randomDips = $dips->random(min(2, $dips->count()));
                $sortOrder = 4;
                
                foreach ($randomDips as $dip) {
                    DB::table('combo_upsell_product')->insert([
                        'combo_id' => $combo->id,
                        'product_id' => $dip->id,
                        'sort_order' => $sortOrder++,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $this->command->info("Added {$dip->name} upsell to: {$combo->name}");
                }
            }
        }
        
        $this->command->info('Combo upsells added successfully');
    }
} 