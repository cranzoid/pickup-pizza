<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

// Get all specialty pizzas
$specialtyPizzas = Product::where('is_specialty', true)->get();

foreach ($specialtyPizzas as $product) {
    // Set has_toppings to true
    $product->has_toppings = true;
    
    // Add the add_ons configuration
    $product->add_ons = json_encode([
        'show_extra_toppings_toggle' => true,
        'extra_topping_price' => [
            'medium' => 1.60,
            'large' => 2.10,
            'xl' => 2.30
        ]
    ]);
    
    // Save the product
    $product->save();
    
    echo "Updated {$product->name} pizza\n";
}

echo "\nAll specialty pizzas have been updated with the ability to add extra toppings.\n"; 