<?php

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Product;

// Get the Wings category
$wingsCategory = Category::where('name', 'Wings')->first();

if (!$wingsCategory) {
    echo "Error: Wings category not found!\n";
    exit(1);
}

// List all products in the Wings category
$wingProducts = Product::where('category_id', $wingsCategory->id)->get();

echo "Products in Wings category (ID: {$wingsCategory->id}):\n";
echo "---------------------------------------------\n";

if ($wingProducts->count() > 0) {
    foreach ($wingProducts as $product) {
        echo "- {$product->name} (ID: {$product->id})\n";
        echo "  Price: \${$product->price}\n";
        echo "  Description: {$product->description}\n";
        echo "  Has Size Options: " . ($product->has_size_options ? 'Yes' : 'No') . "\n";
        
        if ($product->has_size_options && $product->sizes) {
            echo "  Sizes: " . print_r(json_decode($product->sizes, true), true) . "\n";
        }
        
        echo "  Has Extras: " . ($product->has_extras ? 'Yes' : 'No') . "\n";
        echo "---------------------------------------------\n";
    }
    
    echo "Total: " . $wingProducts->count() . " products\n";
} else {
    echo "No products found in Wings category.\n";
} 