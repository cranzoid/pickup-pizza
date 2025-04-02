<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user only if it doesn't exist
        if (!User::where('email', 'admin@pisapizza.ca')->exists()) {
            User::factory()->create([
                'name' => 'Pizza Admin',
                'email' => 'admin@pisapizza.ca',
                'password' => Hash::make('password123'), // Change this in production!
            ]);
        }
        
        // Call the Pisa Pizza menu seeder
        $this->call([
            SettingSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ToppingSeeder::class,
            DiscountSeeder::class,
            CompleteMenuSeeder::class,
            UpdateSinglePizzaSeeder::class,
            PisaPizzaMenuSeeder::class,
            ExtraToppingOptionSeeder::class,
            // Updated combo seeders
            TwoMediumPizzasComboSeeder::class,
            TwoLargePizzasComboSeeder::class,
            TwoXLargePizzasComboSeeder::class,
            UltimatePizzaWingsComboSeeder::class,
            ComboUpsellSeeder::class,
            FixCombosSeeder::class,
            UpdateComboProductsSeeder::class,
            FixComboCategories::class,
        ]);
    }
}
