<?php

/**
 * This script diagnoses issues with specialty pizza images
 * Run this with: php diagnose-images.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// First check if storage link exists
echo "Checking storage symlink...\n";
$publicStorage = public_path('storage');
if (file_exists($publicStorage) && is_link($publicStorage)) {
    echo "✓ Storage symlink exists\n";
    echo "   Symlink points to: " . readlink($publicStorage) . "\n";
} else {
    echo "✗ Storage symlink missing or not a symbolic link!\n";
    echo "   Run 'php artisan storage:link' to create it\n";
}

// Get all specialty pizzas from database
echo "\nChecking database records for specialty pizzas:\n";
$specialtyPizzas = \App\Models\Product::where('is_specialty', true)->get();

if ($specialtyPizzas->isEmpty()) {
    echo "✗ No specialty pizzas found in database!\n";
} else {
    echo "✓ Found " . $specialtyPizzas->count() . " specialty pizzas\n";
    
    foreach ($specialtyPizzas as $pizza) {
        echo "\n" . $pizza->name . ":\n";
        
        if (empty($pizza->image_path)) {
            echo "✗ No image path set in database\n";
            continue;
        }
        
        echo "- Database path: " . $pizza->image_path . "\n";
        
        // Check storage path
        $storagePath = storage_path('app/public/' . $pizza->image_path);
        if (file_exists($storagePath)) {
            echo "✓ Image exists in storage: " . $storagePath . "\n";
        } else {
            echo "✗ Image NOT found in storage: " . $storagePath . "\n";
        }
        
        // Check public path
        $publicPath = public_path('storage/' . $pizza->image_path);
        if (file_exists($publicPath)) {
            echo "✓ Image accessible in public path: " . $publicPath . "\n";
        } else {
            echo "✗ Image NOT accessible in public path: " . $publicPath . "\n";
        }
    }
}

echo "\nDiagnostic Summary:\n";
echo "1. Make sure images are uploaded to: " . storage_path('app/public/products/') . "\n";
echo "2. Run 'php artisan storage:link' to create/refresh the public symlink\n";
echo "3. Verify image filenames match the pattern: specialty-name.jpg\n";
echo "4. Check storage permissions: storage directory should be writable\n"; 