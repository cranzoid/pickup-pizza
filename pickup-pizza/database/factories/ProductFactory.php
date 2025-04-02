<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(2, true);
        $slug = Str::slug($name);
        
        return [
            'name' => $name,
            'slug' => $slug,
            'description' => fake()->paragraph(),
            'image_path' => null,
            'price' => fake()->randomFloat(2, 5, 30),
            'category_id' => Category::factory(),
            'has_extras' => false,
            'is_pizza' => false,
            'is_specialty' => false,
            'has_size_options' => false,
            'has_toppings' => false,
            'active' => true,
            'is_featured' => false,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
    
    /**
     * Indicate that the product has extras.
     */
    public function withExtras(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_extras' => true,
        ]);
    }
    
    /**
     * Indicate that the product is a pizza.
     */
    public function asPizza(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_pizza' => true,
            'has_toppings' => true,
            'has_extras' => true,
            'has_size_options' => true,
            'max_toppings' => fake()->numberBetween(3, 10),
        ]);
    }
    
    /**
     * Indicate that the product is a specialty pizza.
     */
    public function asSpecialtyPizza(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_pizza' => true,
            'is_specialty' => true,
            'has_size_options' => true,
        ]);
    }
    
    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
} 