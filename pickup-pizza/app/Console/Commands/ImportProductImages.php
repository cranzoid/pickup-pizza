<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ImportProductImages extends Command
{
    protected $signature = 'products:import-images {source_dir=product-images}';
    protected $description = 'Import product images from a directory';

    public function handle()
    {
        $sourceDir = $this->argument('source_dir');
        $sourcePath = public_path($sourceDir);
        
        if (!File::isDirectory($sourcePath)) {
            $this->error("Directory not found: {$sourcePath}");
            return 1;
        }
        
        $files = File::files($sourcePath);
        $count = 0;
        $notMatched = 0;
        
        // Create directory if it doesn't exist
        if (!Storage::disk('public')->exists('products')) {
            Storage::disk('public')->makeDirectory('products');
        }
        
        $this->info("Found " . count($files) . " image files in {$sourcePath}");
        
        foreach ($files as $file) {
            $filename = $file->getFilename();
            $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME); // Get filename without extension
            
            // Find product by slug
            $product = Product::where('slug', $nameWithoutExt)->first();
            
            if ($product) {
                // Copy file to storage
                $newPath = 'products/' . $filename;
                Storage::disk('public')->put($newPath, File::get($file));
                
                // Update product with image path
                $product->image_path = $newPath;
                $product->save();
                
                $this->info("✓ Associated image {$filename} with product {$product->name}");
                $count++;
            } else {
                $this->warn("✗ No product found with slug: {$nameWithoutExt}");
                $notMatched++;
            }
        }
        
        $this->info("Summary: Imported {$count} images successfully, {$notMatched} images could not be matched to products");
        return 0;
    }
} 