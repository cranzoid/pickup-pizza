<?php

/**
 * This script fixes pricing and naming issues with specialty pizzas
 * Run this with: php fix-pizza-issues.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "PIZZA FIX SCRIPT\n";
echo "================\n\n";

// Fix Deluxe pizza pricing - reduce by half
$deluxe = \App\Models\Product::where('name', 'Deluxe')
    ->where('is_specialty', true)
    ->first();

if ($deluxe) {
    echo "Found Deluxe pizza (ID: {$deluxe->id})\n";
    
    // Fix pricing - reduce by half
    $sizes = is_array($deluxe->sizes) ? $deluxe->sizes : json_decode($deluxe->sizes, true);
    $correctPrices = [
        'medium' => 15.99,
        'large' => 17.99,
        'xl' => 19.99
    ];
    
    $deluxe->sizes = json_encode($correctPrices);
    $deluxe->save();
    echo "✓ Fixed Deluxe pizza pricing (reduced to correct values)\n";
    
    // Show the updated prices
    echo "Updated prices:\n";
    foreach ($correctPrices as $size => $price) {
        echo "  - $size: $" . number_format($price, 2) . "\n";
    }
} else {
    echo "⚠️ Deluxe pizza not found!\n";
}

// Handle the Meatball vs All Meat pizza issue
$meatball = \App\Models\Product::where('name', 'Meatball Pizza')
    ->where('is_specialty', true)
    ->first();

if ($meatball) {
    echo "\nFound Meatball Pizza (ID: {$meatball->id})\n";
    
    // Check if an All Meat pizza already exists
    $allMeat = \App\Models\Product::where('name', 'All Meat')
        ->where('is_specialty', true)
        ->first();
    
    if ($allMeat) {
        echo "⚠️ An All Meat pizza already exists (ID: {$allMeat->id}).\n";
        echo "Updating the All Meat pizza with correct toppings and pricing.\n";
        
        // Just update toppings and pricing
        updatePizzaToppingsAndPricing($allMeat);
    } else {
        // Rename Meatball Pizza to All Meat
        $oldName = $meatball->name;
        $meatball->name = 'All Meat';
        $meatball->slug = \Illuminate\Support\Str::slug('All Meat');
        $meatball->description = 'For the carnivore! Loaded with pepperoni, Italian sausage, bacon, ham, and ground beef.';
        $meatball->save();
        
        echo "✓ Renamed '{$oldName}' to 'All Meat'\n";
        
        // Update toppings and pricing
        updatePizzaToppingsAndPricing($meatball);
    }
} else {
    // Try to find any meat-related pizza
    $meatPizza = \App\Models\Product::where('name', 'like', '%Meat%')
        ->where('is_specialty', true)
        ->first();
    
    if ($meatPizza) {
        echo "\nFound {$meatPizza->name} pizza (ID: {$meatPizza->id})\n";
        
        // Rename to All Meat
        $oldName = $meatPizza->name;
        $meatPizza->name = 'All Meat';
        $meatPizza->slug = \Illuminate\Support\Str::slug('All Meat');
        $meatPizza->description = 'For the carnivore! Loaded with pepperoni, Italian sausage, bacon, ham, and ground beef.';
        $meatPizza->save();
        
        echo "✓ Renamed '{$oldName}' to 'All Meat'\n";
        
        // Update toppings and pricing
        updatePizzaToppingsAndPricing($meatPizza);
    } else {
        echo "\n⚠️ No meat pizza found to rename to All Meat.\n";
        echo "Creating a new All Meat pizza...\n";
        
        // Get the specialty pizza category
        $specialtyCategory = \App\Models\Category::where('name', 'Specialty Pizzas')->first();
        if (!$specialtyCategory) {
            echo "⚠️ Specialty Pizzas category not found!\n";
            exit(1);
        }
        
        // Create the All Meat pizza
        $allMeat = new \App\Models\Product();
        $allMeat->category_id = $specialtyCategory->id;
        $allMeat->name = 'All Meat';
        $allMeat->slug = \Illuminate\Support\Str::slug('All Meat');
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
        
        echo "✓ Created All Meat pizza (ID: {$allMeat->id})\n";
        
        // Add toppings
        updatePizzaToppingsAndPricing($allMeat);
    }
}

echo "\nDone!\n";

/**
 * Helper function to update toppings and pricing
 */
function updatePizzaToppingsAndPricing($pizza) {
    // Get the necessary toppings
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
        return;
    }
    
    // Remove any existing default toppings
    $pizza->toppings()->detach();
    echo "Removed existing toppings\n";
    
    // Add the default toppings
    foreach ($toppings as $name => $topping) {
        $pizza->toppings()->attach($topping->id, [
            'is_default' => true,
            'quantity' => 1,
        ]);
        echo "Added {$name} as default topping\n";
    }
    
    echo "✓ Fixed pizza toppings\n";
    
    // Fix pricing
    $sizes = is_array($pizza->sizes) ? $pizza->sizes : json_decode($pizza->sizes, true);
    $correctPrices = [
        'medium' => 15.99,
        'large' => 17.99,
        'xl' => 19.99
    ];
    
    $needsPriceUpdate = false;
    foreach ($correctPrices as $size => $price) {
        if (!isset($sizes[$size]) || $sizes[$size] != $price) {
            $needsPriceUpdate = true;
            break;
        }
    }
    
    if ($needsPriceUpdate) {
        $pizza->sizes = json_encode($correctPrices);
        $pizza->save();
        echo "✓ Fixed pizza pricing\n";
        
        // Show the updated prices
        echo "Updated prices:\n";
        foreach ($correctPrices as $size => $price) {
            echo "  - $size: $" . number_format($price, 2) . "\n";
        }
    } else {
        echo "Pricing already correct\n";
    }
} 