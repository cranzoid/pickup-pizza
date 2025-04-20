<?php

/**
 * This script updates the image paths for specialty pizza products
 * Run this with: php database/update-specialty-images.php
 */

require __DIR__ . '/../vendor/autoload.php';

// Load environment file and get database connection
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Map of pizza names to image filenames
$specialtyPizzaImages = [
    'Canadian' => 'specialty-canadian.jpg',
    'Deluxe' => 'specialty-deluxe.jpg',
    'Hawaiian' => 'specialty-hawaiian.jpg',
    'All Meat' => 'specialty-all-meat.jpg',
    'Mexicana' => 'specialty-mexicana.jpg',
    'Vegetarian' => 'specialty-vegetarian.jpg',
    'Pisa' => 'specialty-pisa.jpg',
    'Greek' => 'specialty-greek.jpg',
    'Chicken BBQ' => 'specialty-chicken-bbq.jpg',
    'Butter Chicken' => 'specialty-butter-chicken.jpg',
    'Mediterranean' => 'specialty-mediterranean.jpg',
    'Meatball Pizza' => 'specialty-meatball.jpg',
    'Shawarma Pizza' => 'specialty-shawarma.jpg'
];

// Get the specialty pizza products
$specialtyPizzas = \App\Models\Product::where('is_specialty', true)->get();

echo "Updating image paths for specialty pizzas...\n";

foreach ($specialtyPizzas as $pizza) {
    $pizzaName = $pizza->name;
    
    // Handle "2 For 1" prefix if present
    if (strpos($pizzaName, '2 For 1') === 0) {
        $pizzaName = trim(str_replace('2 For 1', '', $pizzaName));
    }
    
    // Find matching image
    foreach ($specialtyPizzaImages as $name => $image) {
        if (strpos($pizzaName, $name) !== false) {
            // Update image path
            $pizza->image_path = 'products/' . $image;
            $pizza->save();
            
            echo "Updated {$pizza->name} with image {$image}\n";
            break;
        }
    }
}

echo "Done!\n"; 