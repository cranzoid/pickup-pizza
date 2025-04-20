<?php

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

echo "Starting wings category update...\n";

// Get the Wings category
$wingsCategory = Category::where('name', 'Wings')->first();

if (!$wingsCategory) {
    echo "Error: Wings category not found!\n";
    exit(1);
}

// Find wing products in the Sides category
$sidesCategory = Category::where('name', 'Sides')->first();

if ($sidesCategory) {
    $wingProducts = Product::where('category_id', $sidesCategory->id)
        ->where(function($query) {
            $query->where('name', 'like', '%Wing%')
                ->orWhere('name', 'like', '%wings%');
        })
        ->get();
    
    if ($wingProducts->count() > 0) {
        echo "Found " . $wingProducts->count() . " wing products in Sides category.\n";
        
        // Move any wing products to the Wings category
        foreach ($wingProducts as $product) {
            echo "Moving '{$product->name}' to Wings category (ID: {$wingsCategory->id})...\n";
            $product->category_id = $wingsCategory->id;
            $product->save();
        }
        
        echo "Successfully moved wing products to Wings category.\n";
    } else {
        echo "No wing products found in Sides category.\n";
    }
} else {
    echo "Sides category not found.\n";
}

echo "Wing category update completed!\n"; 