<?php

/**
 * This script finds any existing meat pizza products
 * Run this with: php find-existing-meat-pizza.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "SEARCHING FOR MEAT PIZZAS\n";
echo "========================\n\n";

// Find any meat-related pizzas regardless of active status
$meatPizzas = \App\Models\Product::where(function($query) {
    $query->where('name', 'like', '%Meat%')
          ->orWhere('name', 'like', '%meat%')
          ->orWhere('slug', 'like', '%meat%')
          ->orWhere('description', 'like', '%meat%');
})
->orderBy('is_active', 'desc')
->get();

if ($meatPizzas->isEmpty()) {
    echo "No meat pizzas found in the database.\n";
} else {
    echo "Found " . $meatPizzas->count() . " meat-related pizzas:\n\n";
    
    foreach ($meatPizzas as $pizza) {
        echo "ID: {$pizza->id}\n";
        echo "Name: {$pizza->name}\n";
        echo "Slug: {$pizza->slug}\n";
        echo "Active: " . ($pizza->is_active ? "Yes" : "No") . "\n";
        echo "Category ID: {$pizza->category_id}\n";
        echo "Is Specialty: " . ($pizza->is_specialty ? "Yes" : "No") . "\n";
        echo "Description: {$pizza->description}\n";
        
        // Get sizes and prices
        $sizes = is_array($pizza->sizes) ? $pizza->sizes : json_decode($pizza->sizes, true);
        if (!empty($sizes)) {
            echo "Sizes/Prices:\n";
            foreach ($sizes as $size => $price) {
                $priceValue = is_array($price) ? $price['price'] : $price;
                echo "  - $size: $" . number_format($priceValue, 2) . "\n";
            }
        }
        
        echo "\n";
    }
}

echo "Done!\n"; 