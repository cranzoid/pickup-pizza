<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $price = fake()->randomFloat(2, 5, 30);
        $subtotal = $price * $quantity;
        
        return [
            'order_id' => Order::factory(),
            'item_type' => 'product',
            'item_id' => Product::factory(),
            'name' => fake()->words(2, true),
            'size' => fake()->optional()->randomElement(['small', 'medium', 'large', 'extra-large']),
            'quantity' => $quantity,
            'unit_price' => $price,
            'subtotal' => $subtotal,
            'options' => json_encode([]),
            'notes' => fake()->optional(0.3)->sentence(),
            'is_upsell' => false,
        ];
    }
    
    /**
     * Set product data for the order item.
     */
    public function forProduct(Product $product): static
    {
        $quantity = fake()->numberBetween(1, 5);
        $subtotal = $product->price * $quantity;
        
        return $this->state(fn (array $attributes) => [
            'item_type' => 'product',
            'item_id' => $product->id,
            'name' => $product->name,
            'unit_price' => $product->price,
            'quantity' => $quantity,
            'subtotal' => $subtotal,
        ]);
    }
    
    /**
     * Set the item as a combo.
     */
    public function asCombo(): static
    {
        return $this->state(fn (array $attributes) => [
            'item_type' => 'combo',
        ]);
    }
    
    /**
     * Set the item as an upsell.
     */
    public function asUpsell(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_upsell' => true,
        ]);
    }
    
    /**
     * Set options for the order item.
     */
    public function withOptions(array $options): static
    {
        return $this->state(fn (array $attributes) => [
            'options' => json_encode($options),
        ]);
    }
} 