<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DiscountTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test discount code application in cart.
     */
    public function test_discount_code_application(): void
    {
        // Create test data
        $category = Category::factory()->create(['name' => 'Pizzas']);
        $product = Product::factory()->create([
            'name' => 'Test Pizza',
            'price' => 20.00,
            'category_id' => $category->id,
        ]);

        // Create a percentage discount
        $percentDiscount = Discount::factory()->create([
            'code' => 'PERCENT20',
            'type' => 'percentage',
            'value' => 20,
            'active' => true,
            'expires_at' => now()->addDays(30),
            'usage_limit' => 100,
            'usage_count' => 0
        ]);

        // Create a fixed amount discount
        $fixedDiscount = Discount::factory()->create([
            'code' => 'FIXED5',
            'type' => 'fixed',
            'value' => 5,
            'active' => true,
            'expires_at' => now()->addDays(30),
            'usage_limit' => 100,
            'usage_count' => 0
        ]);

        // Create an inactive discount
        $inactiveDiscount = Discount::factory()->create([
            'code' => 'INACTIVE',
            'type' => 'percentage',
            'value' => 10,
            'active' => false,
            'expires_at' => now()->addDays(30),
            'usage_limit' => 100,
            'usage_count' => 0
        ]);

        // Create an expired discount
        $expiredDiscount = Discount::factory()->create([
            'code' => 'EXPIRED',
            'type' => 'percentage',
            'value' => 10,
            'active' => true,
            'expires_at' => now()->subDays(1),
            'usage_limit' => 100,
            'usage_count' => 0
        ]);

        // Add product to cart
        $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // Test valid percentage discount
        $response = $this->post(route('cart.apply-discount'), [
            'discount_code' => 'PERCENT20'
        ]);
        
        $response->assertStatus(302);
        $response->assertSessionHas('discount');
        $discount = session('discount');
        $this->assertEquals('PERCENT20', $discount['code']);
        $this->assertEquals(4.00, $discount['amount']); // 20% of $20.00

        // Clear discount
        $this->post(route('cart.remove-discount'));
        
        // Test valid fixed discount
        $response = $this->post(route('cart.apply-discount'), [
            'discount_code' => 'FIXED5'
        ]);
        
        $response->assertStatus(302);
        $response->assertSessionHas('discount');
        $discount = session('discount');
        $this->assertEquals('FIXED5', $discount['code']);
        $this->assertEquals(5.00, $discount['amount']);

        // Test inactive discount
        $response = $this->post(route('cart.apply-discount'), [
            'discount_code' => 'INACTIVE'
        ]);
        
        $response->assertStatus(302);
        $response->assertSessionHasErrors('discount_code');

        // Test expired discount
        $response = $this->post(route('cart.apply-discount'), [
            'discount_code' => 'EXPIRED'
        ]);
        
        $response->assertStatus(302);
        $response->assertSessionHasErrors('discount_code');

        // Test invalid discount code
        $response = $this->post(route('cart.apply-discount'), [
            'discount_code' => 'INVALID'
        ]);
        
        $response->assertStatus(302);
        $response->assertSessionHasErrors('discount_code');
    }
} 