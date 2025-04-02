<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 10, 100);
        $tax = $subtotal * 0.13; // 13% tax
        $discount = 0;
        $total = $subtotal + $tax - $discount;
        
        return [
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->numerify('##########'),
            'pickup_time' => fake()->dateTimeBetween('now', '+7 days'),
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'discount_amount' => $discount,
            'total' => $total,
            'payment_method' => fake()->randomElement(['stripe', 'pickup']),
            'payment_status' => 'pending',
            'payment_id' => function (array $attributes) {
                return $attributes['payment_method'] === 'stripe' ? 'pm_' . Str::random(24) : null;
            },
            'order_status' => fake()->randomElement(['pending', 'preparing', 'ready', 'picked_up']),
            'order_notes' => fake()->optional(0.3)->sentence(),
        ];
    }
    
    /**
     * Set the order status to pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'order_status' => 'pending',
        ]);
    }
    
    /**
     * Set the order status to preparing.
     */
    public function preparing(): static
    {
        return $this->state(fn (array $attributes) => [
            'order_status' => 'preparing',
        ]);
    }
    
    /**
     * Set the order status to ready.
     */
    public function ready(): static
    {
        return $this->state(fn (array $attributes) => [
            'order_status' => 'ready',
        ]);
    }
    
    /**
     * Set the order status to picked up.
     */
    public function pickedUp(): static
    {
        return $this->state(fn (array $attributes) => [
            'order_status' => 'picked_up',
        ]);
    }
    
    /**
     * Set the payment method to stripe.
     */
    public function withStripePayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'stripe',
            'payment_id' => 'pm_' . Str::random(24),
            'payment_status' => 'paid',
        ]);
    }
    
    /**
     * Set the payment method to pickup.
     */
    public function withPickupPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'pickup',
            'payment_id' => null,
        ]);
    }
} 