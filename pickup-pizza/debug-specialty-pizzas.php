<?php

/**
 * This script diagnoses issues with specific specialty pizzas
 * Run this with: php debug-specialty-pizzas.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "SPECIALTY PIZZA DIAGNOSTIC REPORT\n";
echo "================================\n\n";

// Check for specific pizzas by name
$pizzas = ['Deluxe', 'All Meat'];

foreach ($pizzas as $pizzaName) {
    // Find the pizza
    $pizza = \App\Models\Product::where('name', 'like', "%$pizzaName%")
        ->where('is_specialty', true)
        ->first();
    
    if (!$pizza) {
        echo "⚠️ $pizzaName pizza not found in database!\n";
        continue;
    }
    
    echo "✓ Found $pizzaName pizza in database (ID: {$pizza->id})\n";
    echo "- Name: {$pizza->name}\n";
    echo "- Description: {$pizza->description}\n";
    echo "- Is Specialty: " . ($pizza->is_specialty ? "Yes" : "No") . "\n";
    
    // Check sizes and prices
    echo "- Sizes:\n";
    $sizes = is_array($pizza->sizes) ? $pizza->sizes : json_decode($pizza->sizes, true);
    if (empty($sizes)) {
        echo "  ⚠️ No sizes defined!\n";
    } else {
        foreach ($sizes as $size => $price) {
            $priceValue = is_array($price) ? $price['price'] : $price;
            echo "  - $size: $" . number_format($priceValue, 2) . "\n";
        }
    }
    
    // Check default toppings
    echo "- Default Toppings:\n";
    $toppings = $pizza->defaultToppings()->get();
    if ($toppings->isEmpty()) {
        echo "  ⚠️ No default toppings defined!\n";
    } else {
        foreach ($toppings as $topping) {
            echo "  - {$topping->name}\n";
        }
    }
    
    echo "\n";
}

// Check the defaultToppings relationship in the Product model
echo "Checking Product model defaultToppings relationship:\n";
$productModel = new \ReflectionClass(\App\Models\Product::class);
$methods = $productModel->getMethods();
$hasDefaultToppings = false;

foreach ($methods as $method) {
    if ($method->getName() === 'defaultToppings') {
        $hasDefaultToppings = true;
        echo "✓ Product model has defaultToppings() method\n";
        break;
    }
}

if (!$hasDefaultToppings) {
    echo "⚠️ Product model is missing defaultToppings() method!\n";
}

echo "\nDone!\n"; 