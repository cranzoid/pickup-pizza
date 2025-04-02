<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Create a test category to ensure database is working
        Category::factory()->create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
        
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
