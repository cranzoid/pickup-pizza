<?php

namespace Database\Seeders;

use App\Models\Discount;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $discounts = [
            [
                'code' => 'WELCOME10',
                'description' => 'New customer discount: 10% off your first order',
                'type' => 'percentage',
                'value' => 10,
                'active' => true,
                'expires_at' => Carbon::now()->addMonths(3),
                'usage_limit' => 1000,
                'usage_count' => 0,
            ],
            [
                'code' => 'PIZZA5',
                'description' => '$5 off any order of $30 or more',
                'type' => 'fixed',
                'value' => 5.00,
                'active' => true,
                'expires_at' => Carbon::now()->addDays(30),
                'usage_limit' => null,
                'usage_count' => 0,
            ],
            [
                'code' => 'SPECIAL15',
                'description' => '15% off your order on weekends',
                'type' => 'percentage',
                'value' => 15,
                'active' => true,
                'expires_at' => Carbon::now()->addYear(),
                'usage_limit' => null,
                'usage_count' => 0,
            ],
            [
                'code' => 'FAMILY25',
                'description' => '$25 off family size orders of $100 or more',
                'type' => 'fixed',
                'value' => 25.00,
                'active' => true,
                'expires_at' => null,
                'usage_limit' => null,
                'usage_count' => 0,
            ],
            [
                'code' => 'SUMMER20',
                'description' => 'Summer special: 20% off your order',
                'type' => 'percentage',
                'value' => 20,
                'active' => false, // Inactive discount for testing
                'expires_at' => Carbon::now()->addMonths(2),
                'usage_limit' => 500,
                'usage_count' => 0,
            ],
        ];
        
        foreach ($discounts as $discount) {
            Discount::create($discount);
        }
    }
}
