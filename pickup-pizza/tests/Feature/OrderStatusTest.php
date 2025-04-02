<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderStatusTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test order status updates and notification sending.
     */
    public function test_order_status_updates(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Create test order
        $order = Order::factory()->create([
            'order_status' => 'pending',
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '1234567890',
            'pickup_time' => now()->addDay(),
            'payment_method' => 'pickup',
            'subtotal' => 25.99,
            'tax_amount' => 3.38,
            'discount_amount' => 0,
            'total' => 29.37,
        ]);

        // Add order items
        $category = Category::factory()->create(['name' => 'Pizzas']);
        $product = Product::factory()->create([
            'name' => 'Test Pizza',
            'price' => 25.99,
            'category_id' => $category->id,
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'item_id' => $product->id,
            'item_type' => 'product',
            'name' => $product->name,
            'unit_price' => $product->price,
            'quantity' => 1,
            'options' => json_encode([]),
            'notes' => 'Test notes',
            'subtotal' => $product->price,
        ]);

        // Login as admin
        $this->actingAs($admin);

        // Test updating order status to "preparing"
        $response = $this->post(route('admin.orders.status', $order), [
            'status' => 'preparing',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        
        $order->refresh();
        $this->assertEquals('preparing', $order->order_status);

        // Test updating order status to "ready"
        $response = $this->post(route('admin.orders.status', $order), [
            'status' => 'ready',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        
        $order->refresh();
        $this->assertEquals('ready', $order->order_status);

        // Test updating order status to "picked_up"
        $response = $this->post(route('admin.orders.status', $order), [
            'status' => 'picked_up',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        
        $order->refresh();
        $this->assertEquals('picked_up', $order->order_status);
    }
    
    /**
     * Test order notification sending if mail is implemented.
     */
    public function test_order_notification_sending(): void
    {
        // Skip if the OrderStatusUpdate class doesn't exist
        if (!class_exists('\App\Mail\OrderStatusUpdate')) {
            $this->markTestSkipped('OrderStatusUpdate mail class not implemented yet.');
            return;
        }
        
        // Fake mail to test email notifications
        Mail::fake();

        // Create admin user
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Create test order
        $order = Order::factory()->create([
            'order_status' => 'pending',
            'customer_name' => 'Email Test Customer',
            'customer_email' => 'notification@example.com',
            'customer_phone' => '1234567890',
            'pickup_time' => now()->addHours(2),
            'payment_method' => 'pickup',
            'subtotal' => 25.99,
            'tax_amount' => 3.38,
            'discount_amount' => 0,
            'total' => 29.37,
        ]);

        // Add order items
        $category = Category::factory()->create(['name' => 'Pizzas']);
        $product = Product::factory()->create([
            'name' => 'Test Pizza',
            'price' => 25.99,
            'category_id' => $category->id,
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'item_id' => $product->id,
            'item_type' => 'product',
            'name' => $product->name,
            'unit_price' => $product->price,
            'quantity' => 1,
            'options' => json_encode([]),
            'notes' => 'Test notes',
            'subtotal' => $product->price,
        ]);

        // Login as admin
        $this->actingAs($admin);

        // Sequence of status changes: pending → preparing → ready → picked_up
        $statuses = ['preparing', 'ready', 'picked_up'];
        
        foreach ($statuses as $status) {
            $response = $this->post(route('admin.orders.status', $order), [
                'status' => $status,
            ]);
            
            $response->assertStatus(302);
            $response->assertSessionHas('success');
            
            $order->refresh();
            $this->assertEquals($status, $order->order_status);
            
            if (in_array($status, ['preparing', 'ready'])) {
                Mail::assertSent(\App\Mail\OrderStatusUpdate::class, function ($mail) use ($order) {
                    return $mail->hasTo($order->customer_email) &&
                           $mail->order->id === $order->id;
                });
            }
        }
        
        // Check that we received the correct number of emails (2 - for preparing and ready statuses)
        $this->assertEquals(2, Mail::sent(\App\Mail\OrderStatusUpdate::class)->count());
    }
    
    /**
     * Test order creation notification.
     */
    public function test_order_creation_notification(): void
    {
        // Skip for now as we need to check the checkout process in more detail
        $this->markTestSkipped('Checkout process needs further investigation.');
        return;
    }
} 