<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

try {
    // Start a transaction for safety
    DB::beginTransaction();
    
    echo "Checking for existing products...\n";
    $products = Product::where('is_active', true)->get();
    
    if ($products->count() == 0) {
        echo "No active products found. Please make sure you have products in the database before creating sample orders.\n";
        DB::rollBack();
        exit;
    }
    
    echo "Found " . $products->count() . " products.\n\n";
    
    // Customer information
    $customers = [
        ['name' => 'John Smith', 'email' => 'john@example.com', 'phone' => '5551234567'],
        ['name' => 'Jane Doe', 'email' => 'jane@example.com', 'phone' => '5559876543'],
        ['name' => 'Bob Johnson', 'email' => 'bob@example.com', 'phone' => '5552468013'],
        ['name' => 'Alice Williams', 'email' => 'alice@example.com', 'phone' => '5551357924'],
        ['name' => 'Mike Brown', 'email' => 'mike@example.com', 'phone' => '5553692581'],
        ['name' => 'Sarah Davis', 'email' => 'sarah@example.com', 'phone' => '5557531598'],
    ];
    
    // Create orders for the last 14 days
    $totalCreated = 0;
    $startDate = Carbon::now()->subDays(14);
    
    for ($day = 0; $day <= 14; $day++) {
        $date = $startDate->copy()->addDays($day);
        
        // More orders on weekends, fewer on weekdays
        $isWeekend = $date->isWeekend();
        $orderCount = $isWeekend ? rand(4, 8) : rand(2, 5);
        
        // Today should have a few orders
        if ($day == 14) {
            $orderCount = rand(2, 4);
        }
        
        echo "Creating " . $orderCount . " orders for " . $date->format('Y-m-d') . "...\n";
        
        for ($i = 0; $i < $orderCount; $i++) {
            // Randomly select a customer
            $customer = $customers[array_rand($customers)];
            
            // Generate pickup time (between 11am - 8pm)
            $hour = rand(11, 20);
            $minute = rand(0, 59);
            $pickupTime = $date->copy()->setHour($hour)->setMinute($minute);
            
            // Generate a random order number using the PZ format from the migration
            $orderNumber = 'PZ' . $date->format('Ymd') . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
            
            // Calculate order values
            $subtotal = 0;
            $taxRate = 0.13; // 13% tax
            
            // Create the order
            $order = new Order();
            $order->order_number = $orderNumber;
            $order->customer_name = $customer['name'];
            $order->customer_email = $customer['email'];
            $order->customer_phone = $customer['phone'];
            $order->pickup_time = $pickupTime;
            
            // 80% of orders are paid, 20% are pending
            $paymentStatus = (rand(1, 100) <= 80) ? 'paid' : 'pending';
            $order->payment_status = $paymentStatus;
            
            // Payment method - 60% stripe, 40% pickup
            $paymentMethod = (rand(1, 100) <= 60) ? 'stripe' : 'pickup';
            $order->payment_method = $paymentMethod;
            
            if ($paymentMethod === 'stripe' && $paymentStatus === 'paid') {
                $order->payment_id = 'pm_' . Str::random(24);
            }
            
            // Order status - mostly completed for past orders, pending/preparing for today
            if ($day < 14) {
                $statuses = ['picked_up', 'picked_up', 'picked_up', 'picked_up', 'ready']; // Mostly picked up
                $order->order_status = $statuses[array_rand($statuses)];
            } else {
                $statuses = ['pending', 'pending', 'preparing', 'ready'];
                $order->order_status = $statuses[array_rand($statuses)];
            }
            
            // Add 1-3 items to the order
            $itemCount = rand(1, 3);
            $orderItems = [];
            
            for ($j = 0; $j < $itemCount; $j++) {
                // Randomly select a product
                $product = $products->random();
                
                // Determine quantity (1-2)
                $quantity = rand(1, 2);
                
                // Calculate item price and subtotal
                $price = $product->price;
                $itemSubtotal = $price * $quantity;
                
                $orderItems[] = [
                    'item_type' => 'product',
                    'item_id' => $product->id,
                    'name' => $product->name,
                    'unit_price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $itemSubtotal,
                    'options' => json_encode([]),
                ];
                
                $subtotal += $itemSubtotal;
            }
            
            // Calculate order totals
            $tax = round($subtotal * $taxRate, 2);
            $total = $subtotal + $tax;
            
            $order->subtotal = $subtotal;
            $order->tax_amount = $tax;
            $order->total = $total;
            
            // Set created_at timestamp to the given date
            $order->created_at = $date->copy()->setHour(rand(9, 20))->setMinute(rand(0, 59));
            $order->updated_at = $order->created_at;
            
            // Save the order
            $order->save();
            
            // Save the order items
            foreach ($orderItems as $item) {
                $orderItem = new OrderItem($item);
                $order->items()->save($orderItem);
            }
            
            $totalCreated++;
        }
    }
    
    // Commit the transaction
    DB::commit();
    
    echo "\nSample order creation complete!\n";
    echo "Total orders created: " . $totalCreated . "\n";
    echo "Total orders in database: " . Order::count() . "\n";
    
} catch (Exception $e) {
    // Rollback the transaction in case of error
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
} 