<?php

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Product;

// Find all products containing "wing" in name
$wingProducts = Product::where('name', 'like', '%wing%')
    ->orWhere('name', 'like', '%Wing%')
    ->orWhere('description', 'like', '%wing%')
    ->orWhere('description', 'like', '%Wing%')
    ->get();

echo "All wing-related products in the database:\n";
echo "---------------------------------------------\n";

if ($wingProducts->count() > 0) {
    foreach ($wingProducts as $product) {
        $category = Category::find($product->category_id);
        $categoryName = $category ? $category->name : 'Unknown Category';
        
        echo "- {$product->name} (ID: {$product->id})\n";
        echo "  Category: {$categoryName} (ID: {$product->category_id})\n";
        echo "  Price: \${$product->price}\n";
        echo "  Description: {$product->description}\n";
        echo "  Has Size Options: " . ($product->has_size_options ? 'Yes' : 'No') . "\n";
        
        if ($product->has_size_options && $product->sizes) {
            $sizes = is_string($product->sizes) ? json_decode($product->sizes, true) : $product->sizes;
            echo "  Sizes: " . print_r($sizes, true) . "\n";
        }
        
        echo "  Has Extras: " . ($product->has_extras ? 'Yes' : 'No') . "\n";
        echo "---------------------------------------------\n";
    }
    
    echo "Total: " . $wingProducts->count() . " products\n";
} else {
    echo "No wing-related products found in the database.\n";
} 