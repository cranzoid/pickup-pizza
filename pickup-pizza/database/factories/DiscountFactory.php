<?php

namespace Database\Factories;

use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discount>
 */
class DiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(8)),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(['fixed', 'percentage']),
            'value' => fake()->randomFloat(2, 5, 50),
            'usage_limit' => fake()->optional()->numberBetween(10, 1000),
            'usage_count' => 0,
            'expires_at' => fake()->optional()->dateTimeBetween('+1 week', '+6 months'),
            'active' => true,
        ];
    }
    
    /**
     * Set the discount as a fixed amount.
     */
    public function asFixed(float $amount = null): static
    {
        return $this->state(function (array $attributes) use ($amount) {
            return [
                'type' => 'fixed',
                'value' => $amount ?? fake()->randomFloat(2, 5, 30),
            ];
        });
    }
    
    /**
     * Set the discount as a percentage off.
     */
    public function asPercentage(float $percent = null): static
    {
        return $this->state(function (array $attributes) use ($percent) {
            return [
                'type' => 'percentage',
                'value' => $percent ?? fake()->randomFloat(2, 5, 50),
            ];
        });
    }
    
    /**
     * Set the discount as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
    
    /**
     * Set the discount as expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => fake()->dateTimeBetween('-3 months', '-1 day'),
        ]);
    }
    
    /**
     * Set the discount with a specific usage count.
     */
    public function withUsageCount(int $count): static
    {
        return $this->state(fn (array $attributes) => [
            'usage_count' => $count,
        ]);
    }
} 
