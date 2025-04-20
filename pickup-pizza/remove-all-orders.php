<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use Illuminate\Support\Facades\DB;

try {
    // Start a transaction for safety
    DB::beginTransaction();
    
    // Get all orders
    $orders = Order::all();
    
    echo "Total orders before cleanup: " . $orders->count() . "\n\n";
    
    if ($orders->count() > 0) {
        foreach ($orders as $order) {
            echo "Deleting order #" . $order->order_number . " for customer: " . $order->customer_name . "\n";
            $order->delete();
        }
        
        // Commit the transaction
        DB::commit();
        
        echo "\nCleanup complete!\n";
        echo "All orders deleted successfully.\n";
        echo "Total orders now: " . Order::count() . "\n";
    } else {
        echo "No orders to delete.\n";
        DB::rollBack();
    }
    
} catch (Exception $e) {
    // Rollback the transaction in case of error
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
} 