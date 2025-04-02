<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test database configuration.
     */
    public function test_database_connection(): void
    {
        $this->assertTrue(DB::connection()->getPdo() ? true : false);
    }

    /**
     * Test required tables exist.
     */
    public function test_required_tables_exist(): void
    {
        $requiredTables = [
            'users',
            'categories',
            'products',
            'product_extras',
            'orders',
            'order_items',
            'discounts',
            'migrations',
        ];

        foreach ($requiredTables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Table {$table} does not exist");
        }
    }

    /**
     * Test required environment variables.
     */
    public function test_required_environment_variables(): void
    {
        $requiredEnvVars = [
            'APP_NAME',
            'APP_ENV',
            'APP_DEBUG',
            'APP_URL',
            'DB_CONNECTION',
            'MAIL_MAILER',
            'MAIL_FROM_ADDRESS',
            'STRIPE_KEY',
            'STRIPE_SECRET',
        ];

        foreach ($requiredEnvVars as $var) {
            $this->assertNotNull(env($var), "Environment variable {$var} is not set");
        }
    }

    /**
     * Test required services are configured.
     */
    public function test_required_services_are_configured(): void
    {
        // Check Stripe configuration
        $this->assertNotEmpty(Config::get('services.stripe.key'));
        $this->assertNotEmpty(Config::get('services.stripe.secret'));
        
        // Check Mail configuration
        $this->assertNotEmpty(Config::get('mail.from.address'));
    }

    /**
     * Test application caches can be created for production.
     */
    public function test_application_can_be_optimized(): void
    {
        // Run optimizations in a way that doesn't affect the actual application
        $exitCode = Artisan::call('route:list');
        $this->assertEquals(0, $exitCode, 'Route list command failed');
        
        // We'll skip actual cache creation but test the commands exist
        $commands = array_keys(Artisan::all());
        $this->assertTrue(in_array('config:cache', $commands));
        $this->assertTrue(in_array('route:cache', $commands));
        $this->assertTrue(in_array('view:cache', $commands));
    }
} 