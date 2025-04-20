<?php

/**
 * This script fixes issues with specialty pizzas
 * Run this with: php fix-specialty-pizzas.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "SPECIALTY PIZZA FIX SCRIPT\n";
echo "=========================\n\n";

// Fix Deluxe pizza toppings
$deluxe = \App\Models\Product::where('name', 'Deluxe')
    ->where('is_specialty', true)
    ->first();

if ($deluxe) {
    echo "Found Deluxe pizza (ID: {$deluxe->id})\n";
    
    // Get the necessary toppings
    $toppings = [
        'Pepperoni' => \App\Models\Topping::where('name', 'Pepperoni')->first(),
        'Mushrooms' => \App\Models\Topping::where('name', 'Mushrooms')->first(),
        'Green Peppers' => \App\Models\Topping::where('name', 'Green Peppers')->first(),
        'Onions' => \App\Models\Topping::where('name', 'Onions')->first(),
        'Bacon' => \App\Models\Topping::where('name', 'Real Bacon')->first() ?? \App\Models\Topping::where('name', 'Bacon')->first(),
        'Tomatoes' => \App\Models\Topping::where('name', 'Tomatoes')->first(),
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
        $deluxe->toppings()->detach();
        echo "Removed existing toppings\n";
        
        // Add the default toppings
        foreach ($toppings as $name => $topping) {
            $deluxe->toppings()->attach($topping->id, [
                'is_default' => true,
                'quantity' => 1,
            ]);
            echo "Added {$name} as default topping\n";
        }
        
        echo "✓ Fixed Deluxe pizza toppings\n\n";
    }
    
    // Fix pricing if needed
    $sizes = is_array($deluxe->sizes) ? $deluxe->sizes : json_decode($deluxe->sizes, true);
    $correctPrices = [
        'medium' => 30.99,
        'large' => 33.99,
        'xl' => 38.99
    ];
    
    $needsPriceUpdate = false;
    foreach ($correctPrices as $size => $price) {
        if (!isset($sizes[$size]) || $sizes[$size] != $price) {
            $needsPriceUpdate = true;
            break;
        }
    }
    
    if ($needsPriceUpdate) {
        $deluxe->sizes = json_encode($correctPrices);
        $deluxe->save();
        echo "✓ Fixed Deluxe pizza pricing\n";
    } else {
        echo "Pricing already correct\n";
    }
} else {
    echo "⚠️ Deluxe pizza not found!\n";
}

// Check if All Meat already exists with correct slug
$allMeatCorrect = \App\Models\Product::where('name', 'All Meat')
    ->where('is_specialty', true)
    ->first();

if ($allMeatCorrect) {
    echo "\nFound All Meat pizza (ID: {$allMeatCorrect->id})\n";
    
    // Just fix the toppings and pricing
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
    } else {
        // Remove any existing default toppings
        $allMeatCorrect->toppings()->detach();
        echo "Removed existing toppings\n";
        
        // Add the default toppings
        foreach ($toppings as $name => $topping) {
            $allMeatCorrect->toppings()->attach($topping->id, [
                'is_default' => true,
                'quantity' => 1,
            ]);
            echo "Added {$name} as default topping\n";
        }
        
        echo "✓ Fixed All Meat pizza toppings\n";
    }
    
    // Fix pricing if needed
    $sizes = is_array($allMeatCorrect->sizes) ? $allMeatCorrect->sizes : json_decode($allMeatCorrect->sizes, true);
    $correctPrices = [
        'medium' => 30.99,
        'large' => 33.99,
        'xl' => 38.99
    ];
    
    $needsPriceUpdate = false;
    foreach ($correctPrices as $size => $price) {
        if (!isset($sizes[$size]) || $sizes[$size] != $price) {
            $needsPriceUpdate = true;
            break;
        }
    }
    
    if ($needsPriceUpdate) {
        $allMeatCorrect->sizes = json_encode($correctPrices);
        $allMeatCorrect->save();
        echo "✓ Fixed All Meat pizza pricing\n";
    } else {
        echo "Pricing already correct\n";
    }
} else {
    // Fix Meat Lovers pizza instead (if it exists)
    $meatLovers = \App\Models\Product::where('name', 'Meat Lovers')
        ->where('is_specialty', true)
        ->first();
        
    if ($meatLovers) {
        echo "\nFound Meat Lovers pizza (ID: {$meatLovers->id})\n";
        
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
        } else {
            // Remove any existing default toppings
            $meatLovers->toppings()->detach();
            echo "Removed existing toppings\n";
            
            // Add the default toppings
            foreach ($toppings as $name => $topping) {
                $meatLovers->toppings()->attach($topping->id, [
                    'is_default' => true,
                    'quantity' => 1,
                ]);
                echo "Added {$name} as default topping\n";
            }
            
            echo "✓ Fixed Meat Lovers pizza toppings\n";
        }
        
        // Fix pricing if needed
        $sizes = is_array($meatLovers->sizes) ? $meatLovers->sizes : json_decode($meatLovers->sizes, true);
        $correctPrices = [
            'medium' => 30.99,
            'large' => 33.99,
            'xl' => 38.99
        ];
        
        $needsPriceUpdate = false;
        foreach ($correctPrices as $size => $price) {
            if (!isset($sizes[$size]) || $sizes[$size] != $price) {
                $needsPriceUpdate = true;
                break;
            }
        }
        
        if ($needsPriceUpdate) {
            $meatLovers->sizes = json_encode($correctPrices);
            $meatLovers->save();
            echo "✓ Fixed Meat Lovers pizza pricing\n";
        } else {
            echo "Pricing already correct\n";
        }
    } else {
        // Fix "Meatball Pizza" or any other meat-related pizza
        $otherMeatPizza = \App\Models\Product::where('name', 'like', '%Meat%')
            ->where('is_specialty', true)
            ->first();
            
        if ($otherMeatPizza) {
            echo "\nFound {$otherMeatPizza->name} pizza (ID: {$otherMeatPizza->id})\n";
            
            // Just fix the toppings and pricing for this pizza
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
            } else {
                // Remove any existing default toppings
                $otherMeatPizza->toppings()->detach();
                echo "Removed existing toppings\n";
                
                // Add the default toppings
                foreach ($toppings as $name => $topping) {
                    $otherMeatPizza->toppings()->attach($topping->id, [
                        'is_default' => true,
                        'quantity' => 1,
                    ]);
                    echo "Added {$name} as default topping\n";
                }
                
                echo "✓ Fixed {$otherMeatPizza->name} pizza toppings\n";
            }
            
            // Fix pricing if needed
            $sizes = is_array($otherMeatPizza->sizes) ? $otherMeatPizza->sizes : json_decode($otherMeatPizza->sizes, true);
            $correctPrices = [
                'medium' => 30.99,
                'large' => 33.99,
                'xl' => 38.99
            ];
            
            $needsPriceUpdate = false;
            foreach ($correctPrices as $size => $price) {
                if (!isset($sizes[$size]) || $sizes[$size] != $price) {
                    $needsPriceUpdate = true;
                    break;
                }
            }
            
            if ($needsPriceUpdate) {
                $otherMeatPizza->sizes = json_encode($correctPrices);
                $otherMeatPizza->save();
                echo "✓ Fixed {$otherMeatPizza->name} pizza pricing\n";
            } else {
                echo "Pricing already correct\n";
            }
        } else {
            echo "\n⚠️ No meat pizza found! You may need to create one manually.\n";
        }
    }
}

echo "\nDone!\n"; 