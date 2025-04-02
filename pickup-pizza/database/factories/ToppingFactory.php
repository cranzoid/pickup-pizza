<?php

namespace Database\Factories;

use App\Models\Topping;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Topping>
 */
class ToppingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['meat', 'veggie', 'cheese', 'sauce', 'spice'];
        
        return [
            'name' => fake()->word(),
            'category' => fake()->randomElement($categories),
            'counts_as' => 1,
            'price_factor' => 1.0,
            'display_order' => fake()->numberBetween(1, 100),
            'is_active' => true,
        ];
    }
    
    /**
     * Configure the factory for a meat topping.
     */
    public function meat(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'meat',
        ]);
    }
    
    /**
     * Configure the factory for a vegetable topping.
     */
    public function veggie(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'veggie',
        ]);
    }
    
    /**
     * Configure the factory for a cheese topping.
     */
    public function cheese(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'cheese',
        ]);
    }
    
    /**
     * Configure the factory for a premium topping with a higher price factor.
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'price_factor' => 1.5,
        ]);
    }
    
    /**
     * Configure the factory for an inactive topping.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
} 