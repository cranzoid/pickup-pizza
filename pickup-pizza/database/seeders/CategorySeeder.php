<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Specialty Pizzas',
                'description' => 'Our chef-crafted specialty pizzas with perfect combinations of premium toppings.',
                'sort_order' => 1,
                'is_daily_special' => false,
            ],
            [
                'name' => 'Build Your Own Pizza',
                'description' => 'Create your perfect pizza with your choice of fresh toppings.',
                'sort_order' => 2,
                'is_daily_special' => false,
            ],
            [
                'name' => 'Combos',
                'description' => 'Value-packed meal deals that include pizzas, wings, and drinks.',
                'sort_order' => 3,
                'is_daily_special' => false,
            ],
            [
                'name' => 'Wings',
                'description' => 'Jumbo wings with your choice of rubs and sauces.',
                'sort_order' => 4,
                'is_daily_special' => false,
            ],
            [
                'name' => 'Sides',
                'description' => 'Garlic bread, dips, and other delicious sides.',
                'sort_order' => 5,
                'is_daily_special' => false,
            ],
            [
                'name' => 'Drinks',
                'description' => 'Refreshing beverages to complete your meal.',
                'sort_order' => 6,
                'is_daily_special' => false,
            ],
            [
                'name' => 'Monday Special',
                'description' => 'Special deals available only on Mondays.',
                'sort_order' => 7,
                'is_daily_special' => true,
                'day_of_week' => 'monday',
            ],
            [
                'name' => 'Tuesday Special',
                'description' => 'Special deals available only on Tuesdays.',
                'sort_order' => 8,
                'is_daily_special' => true,
                'day_of_week' => 'tuesday',
            ],
            [
                'name' => 'Wednesday Special',
                'description' => 'Special deals available only on Wednesdays.',
                'sort_order' => 9,
                'is_daily_special' => true,
                'day_of_week' => 'wednesday',
            ],
            [
                'name' => 'Thursday Special',
                'description' => 'Special deals available only on Thursdays.',
                'sort_order' => 10,
                'is_daily_special' => true,
                'day_of_week' => 'thursday',
            ],
            [
                'name' => 'Weekend Special',
                'description' => 'Special deals available on Fridays, Saturdays, and Sundays.',
                'sort_order' => 11,
                'is_daily_special' => true,
                'day_of_week' => 'weekend',
            ],
        ];
        
        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'sort_order' => $category['sort_order'],
                'active' => true,
                'is_daily_special' => $category['is_daily_special'],
                'day_of_week' => $category['day_of_week'] ?? null,
            ]);
        }
    }
}
