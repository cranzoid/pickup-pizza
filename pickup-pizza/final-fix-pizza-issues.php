<?php

/**
 * This script fixes pricing and All Meat pizza issues
 * Run this with: php final-fix-pizza-issues.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "FINAL PIZZA FIX SCRIPT\n";
echo "=====================\n\n";

// 1. Fix Deluxe pizza pricing
$deluxe = \App\Models\Product::where('name', 'Deluxe')
    ->where('is_specialty', true)
    ->first();

if ($deluxe) {
    echo "Found Deluxe pizza (ID: {$deluxe->id})\n";
    
    // Fix pricing
    $correctPrices = [
        'medium' => 15.99,
        'large' => 17.99,
        'xl' => 19.99
    ];
    
    $deluxe->sizes = json_encode($correctPrices);
    $deluxe->save();
    echo "✓ Fixed Deluxe pizza pricing\n";
    echo "Updated prices:\n";
    foreach ($correctPrices as $size => $price) {
        echo "  - $size: $" . number_format($price, 2) . "\n";
    }
    echo "\n";
} else {
    echo "⚠️ Deluxe pizza not found!\n\n";
}

// 2. Find and fix the All Meat Pizza
$allMeat = \App\Models\Product::where('name', 'All Meat')->first();

if ($allMeat) {
    echo "Found All Meat pizza (ID: {$allMeat->id})\n";
    
    // Update to make it a specialty pizza with correct attributes
    $allMeat->is_specialty = true;
    $allMeat->is_pizza = true;
    $allMeat->category_id = $deluxe ? $deluxe->category_id : 1; // Use the same category as Deluxe pizza
    $allMeat->description = 'For the carnivore! Loaded with pepperoni, Italian sausage, bacon, ham, and ground beef.';
    $allMeat->image_path = 'products/specialty-all-meat.jpg';
    $allMeat->has_size_options = true;
    $allMeat->has_toppings = true;
    
    // Set correct pricing
    $correctPrices = [
        'medium' => 15.99,
        'large' => 17.99,
        'xl' => 19.99
    ];
    
    $allMeat->sizes = json_encode($correctPrices);
    $allMeat->save();
    
    echo "✓ Updated All Meat pizza to be a specialty pizza\n";
    echo "✓ Updated description and image path\n";
    echo "✓ Set correct pricing\n";
    
    // Add toppings
    $toppings = [
        'Pepperoni' => \App\Models\Topping::where('name', 'Pepperoni')->first(),
        'Italian Sausage' => \App\Models\Topping::where('name', 'Italian Sausage')->first(),
        'Bacon' => \App\Models\Topping::where('name', 'Real Bacon')->first() ?? \App\Models\Topping::where('name', 'Bacon')->first(),
        'Ham' => \App\Models\Topping::where('name', 'Ham')->first(),
        'Ground Beef' => \App\Models\Topping::where('name', 'Ground Beef')->first(),
    ];
    
    // Check if toppings exist
    $missingToppings = [];
    foreach ($toppings as $name => $topping) {
        if (!$topping) {
            $missingToppings[] = $name;
        }
    }
    
    if (!empty($missingToppings)) {
        echo "⚠️ Missing toppings: " . implode(', ', $missingToppings) . "\n";
    } else {
        // Remove any existing default toppings
        $allMeat->toppings()->detach();
        
        // Add the default toppings
        foreach ($toppings as $name => $topping) {
            $allMeat->toppings()->attach($topping->id, [
                'is_default' => true,
                'quantity' => 1,
            ]);
            echo "Added {$name} as default topping\n";
        }
        
        echo "✓ Updated All Meat pizza toppings\n";
    }
    
    // Hide the Meatball Pizza specialty pizza since we now have All Meat
    $meatball = \App\Models\Product::where('name', 'Meatball Pizza')
        ->where('is_specialty', true)
        ->first();
    
    if ($meatball) {
        $meatball->is_active = false;
        $meatball->save();
        echo "\n✓ Deactivated the Meatball Pizza (ID: {$meatball->id})\n";
    }
} else {
    echo "⚠️ All Meat pizza not found! Creating a new one...\n";
    
    // Get specialty category ID
    $categoryId = $deluxe ? $deluxe->category_id : 1;
    
    // Create the All Meat pizza
    $allMeat = new \App\Models\Product();
    $allMeat->category_id = $categoryId;
    $allMeat->name = 'All Meat';
    $allMeat->slug = 'all-meat-specialty';  // Use a different slug to avoid conflicts
    $allMeat->description = 'For the carnivore! Loaded with pepperoni, Italian sausage, bacon, ham, and ground beef.';
    $allMeat->image_path = 'products/specialty-all-meat.jpg';
    $allMeat->price = 0;
    $allMeat->sizes = json_encode([
        'medium' => 15.99,
        'large' => 17.99,
        'xl' => 19.99
    ]);
    $allMeat->max_toppings = 0;
    $allMeat->is_pizza = true;
    $allMeat->is_specialty = true;
    $allMeat->has_size_options = true;
    $allMeat->has_toppings = true;
    $allMeat->is_active = true;
    $allMeat->sort_order = 2;
    $allMeat->save();
    
    echo "✓ Created All Meat specialty pizza (ID: {$allMeat->id})\n";
    
    // Add toppings
    $toppings = [
        'Pepperoni' => \App\Models\Topping::where('name', 'Pepperoni')->first(),
        'Italian Sausage' => \App\Models\Topping::where('name', 'Italian Sausage')->first(),
        'Bacon' => \App\Models\Topping::where('name', 'Real Bacon')->first() ?? \App\Models\Topping::where('name', 'Bacon')->first(),
        'Ham' => \App\Models\Topping::where('name', 'Ham')->first(),
        'Ground Beef' => \App\Models\Topping::where('name', 'Ground Beef')->first(),
    ];
    
    // Check if toppings exist
    $missingToppings = [];
    foreach ($toppings as $name => $topping) {
        if (!$topping) {
            $missingToppings[] = $name;
        }
    }
    
    if (!empty($missingToppings)) {
        echo "⚠️ Missing toppings: " . implode(', ', $missingToppings) . "\n";
    } else {
        // Add the default toppings
        foreach ($toppings as $name => $topping) {
            $allMeat->toppings()->attach($topping->id, [
                'is_default' => true,
                'quantity' => 1,
            ]);
            echo "Added {$name} as default topping\n";
        }
        
        echo "✓ Added toppings to All Meat pizza\n";
    }
}

echo "\nDone!\n"; 