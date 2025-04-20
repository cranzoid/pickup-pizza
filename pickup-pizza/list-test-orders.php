<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;

// Get all orders
$orders = Order::all();

echo "Total orders: " . $orders->count() . "\n\n";
echo str_pad("ID", 5) . " | " . 
     str_pad("Order #", 15) . " | " . 
     str_pad("Customer Name", 30) . " | " .
     str_pad("Status", 12) . " | " .
     str_pad("Total", 10) . " | " .
     "Created At\n";

echo str_repeat("-", 100) . "\n";

foreach ($orders as $order) {
    echo str_pad($order->id, 5) . " | " . 
         str_pad($order->order_number, 15) . " | " . 
         str_pad($order->customer_name, 30) . " | " .
         str_pad($order->order_status, 12) . " | " .
         str_pad('$' . number_format($order->total, 2), 10) . " | " .
         $order->created_at . "\n";
} 