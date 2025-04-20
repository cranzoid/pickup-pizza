<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use Illuminate\Support\Facades\DB;

// Real customer names to keep
$realCustomers = [
    'Vishesh',
    'Vishesh Vaibhav',
    'MOMO',
    'Aayush Sharma'
];

try {
    // Start a transaction for safety
    DB::beginTransaction();
    
    // Get all orders
    $orders = Order::all();
    
    echo "Total orders before cleanup: " . $orders->count() . "\n\n";
    
    // Counter for deleted orders
    $deletedCount = 0;
    $keptCount = 0;
    
    foreach ($orders as $order) {
        $isRealCustomer = false;
        
        // Check if this is a real customer
        foreach ($realCustomers as $realCustomer) {
            if (stripos($order->customer_name, $realCustomer) !== false) {
                $isRealCustomer = true;
                break;
            }
        }
        
        if (!$isRealCustomer) {
            echo "Deleting order #" . $order->order_number . " for customer: " . $order->customer_name . "\n";
            $order->delete();
            $deletedCount++;
        } else {
            echo "Keeping order #" . $order->order_number . " for customer: " . $order->customer_name . "\n";
            $keptCount++;
        }
    }
    
    // Commit the transaction
    DB::commit();
    
    echo "\nCleanup complete!\n";
    echo "Orders deleted: " . $deletedCount . "\n";
    echo "Orders kept: " . $keptCount . "\n";
    echo "Total orders now: " . Order::count() . "\n";
    
} catch (Exception $e) {
    // Rollback the transaction in case of error
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
} 