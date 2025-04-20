<?php

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Get all tables from SQLite
$tables = DB::connection('sqlite')->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");

echo "Starting migration from SQLite to MySQL...\n";

// Configure MySQL connection details
$mysqlConfig = [
    'driver' => 'mysql',
    'host' => env('MYSQL_HOST', '127.0.0.1'),
    'port' => env('MYSQL_PORT', '3306'),
    'database' => env('MYSQL_DATABASE', 'pisa_pizza'),
    'username' => env('MYSQL_USERNAME', 'root'),
    'password' => env('MYSQL_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
];

// Add the MySQL connection configuration dynamically
config(['database.connections.mysql_new' => $mysqlConfig]);

// First, migrate the schema to MySQL
echo "Migrating schema to MySQL...\n";
try {
    Artisan::call('migrate', [
        '--database' => 'mysql_new',
        '--path' => 'database/migrations',
        '--force' => true,
    ]);
    echo Artisan::output();
} catch (Exception $e) {
    echo "Error migrating schema: " . $e->getMessage() . "\n";
    exit(1);
}

// Now, copy data table by table
echo "Copying data from SQLite to MySQL...\n";
foreach ($tables as $table) {
    $tableName = $table->name;
    
    // Skip Laravel migration table and certain system tables if needed
    if ($tableName == 'migrations' || $tableName == 'personal_access_tokens' || $tableName == 'password_reset_tokens') {
        echo "Skipping system table: $tableName\n";
        continue;
    }
    
    echo "Processing table: $tableName\n";
    
    // Get data from SQLite
    $rows = DB::connection('sqlite')->table($tableName)->get();
    $count = count($rows);
    
    if ($count > 0) {
        echo "  - Found $count rows to migrate\n";
        
        // Insert in batches to avoid memory issues
        $chunks = array_chunk($rows->toArray(), 100);
        
        foreach ($chunks as $chunk) {
            // Convert stdClass objects to arrays
            $records = [];
            foreach ($chunk as $row) {
                $records[] = (array) $row;
            }
            
            // Insert into MySQL
            try {
                DB::connection('mysql_new')->table($tableName)->insert($records);
            } catch (Exception $e) {
                echo "  - Error inserting data for table $tableName: " . $e->getMessage() . "\n";
                // Continue with next table on error
                continue 2;
            }
        }
        
        echo "  - Successfully migrated data for table: $tableName\n";
    } else {
        echo "  - No data to migrate for table: $tableName\n";
    }
}

echo "Migration completed!\n";
echo "Please update your .env file to use MySQL as the default database connection.\n"; 