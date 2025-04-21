<?php

/**
 * Enhanced SQLite to MySQL Migration Script
 * -----------------------------------------
 * This script migrates data from SQLite to MySQL with proper type handling,
 * auto-increment sequence fixing, and robust error handling.
 * 
 * Usage:
 * 1. Make sure your .env file exists with SQLite as the current database
 * 2. Create environment variables for the MySQL target:
 *    - MYSQL_HOST (default: 127.0.0.1)
 *    - MYSQL_PORT (default: 3306)
 *    - MYSQL_DATABASE (the name of your target MySQL database, must exist!)
 *    - MYSQL_USERNAME (your MySQL username)
 *    - MYSQL_PASSWORD (your MySQL password)
 * 3. Run: php migrate-to-mysql-enhanced.php
 */

// Start time tracking for performance measurement
$startTime = microtime(true);

// Require autoloader
require 'vendor/autoload.php';

// Bootstrap Laravel (requiring the app gives us the container, config, etc.)
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Import necessary classes
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;

// Helper function to format time
function formatElapsedTime($seconds) {
    $minutes = floor($seconds / 60);
    $seconds = $seconds % 60;
    return "{$minutes}m {$seconds}s";
}

// Helper function to pretty print messages with timestamp
function logMessage($message, $isError = false) {
    $timestamp = Carbon::now()->format('Y-m-d H:i:s');
    $prefix = $isError ? "\033[31m[ERROR]\033[0m" : "\033[32m[INFO]\033[0m";
    echo "{$prefix} [{$timestamp}] {$message}" . PHP_EOL;
}

// Validate that SQLite is the current database
if (config('database.default') !== 'sqlite') {
    logMessage("Your current database connection is not set to 'sqlite'. This script is designed to migrate from SQLite to MySQL.", true);
    logMessage("Please check your .env file and ensure DB_CONNECTION=sqlite", true);
    exit(1);
}

// Banner
echo PHP_EOL;
echo "╔═════════════════════════════════════════════════════════╗" . PHP_EOL;
echo "║              SQLITE TO MYSQL MIGRATION TOOL             ║" . PHP_EOL;
echo "║                  Enhanced Migration Script              ║" . PHP_EOL;
echo "╚═════════════════════════════════════════════════════════╝" . PHP_EOL;
echo PHP_EOL;

// Verify SQLite database exists
$sqliteDbPath = config('database.connections.sqlite.database');
if (!file_exists($sqliteDbPath)) {
    logMessage("SQLite database file not found at: {$sqliteDbPath}", true);
    exit(1);
}

logMessage("Found SQLite database at: {$sqliteDbPath}");

// Configure MySQL connection details with proper defaults and validation
$mysqlConfig = [
    'driver' => 'mysql',
    'url' => env('MYSQL_URL'),
    'host' => env('MYSQL_HOST', '127.0.0.1'),
    'port' => env('MYSQL_PORT', '3306'),
    'database' => env('MYSQL_DATABASE'),
    'username' => env('MYSQL_USERNAME'),
    'password' => env('MYSQL_PASSWORD'),
    'unix_socket' => env('MYSQL_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => 'InnoDB',
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        // Ensure proper handling of BOOLEAN type between SQLite and MySQL
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    ]) : [],
];

// Validate MySQL configuration
if (empty($mysqlConfig['database'])) {
    logMessage("MYSQL_DATABASE environment variable is missing. Please set it before running this script.", true);
    exit(1);
}

if (empty($mysqlConfig['username'])) {
    logMessage("MYSQL_USERNAME environment variable is missing. Please set it before running this script.", true);
    exit(1);
}

// Add the MySQL connection configuration dynamically
config(['database.connections.mysql_target' => $mysqlConfig]);

// Test MySQL connection
try {
    DB::connection('mysql_target')->getPdo();
    logMessage("Successfully connected to MySQL database '{$mysqlConfig['database']}' on {$mysqlConfig['host']}:{$mysqlConfig['port']}");
} catch (\Exception $e) {
    logMessage("Failed to connect to MySQL database: " . $e->getMessage(), true);
    exit(1);
}

// Get all tables from SQLite
$tables = DB::connection('sqlite')->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");

if (empty($tables)) {
    logMessage("No tables found in SQLite database. Nothing to migrate.", true);
    exit(1);
}

logMessage("Found " . count($tables) . " tables in SQLite database.");

// First, reset MySQL database (drop all tables)
$resetDb = true; // Change to false if you want to preserve some tables
if ($resetDb) {
    logMessage("Preparing MySQL database (dropping existing tables)...");
    
    // Disable foreign key checks temporarily to avoid constraint issues during drops
    DB::connection('mysql_target')->statement('SET FOREIGN_KEY_CHECKS=0');
    
    // Get all tables in MySQL target database
    $mysqlTables = DB::connection('mysql_target')
        ->select("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?", [$mysqlConfig['database']]);
    
    // Drop each table
    foreach ($mysqlTables as $table) {
        $tableName = $table->TABLE_NAME;
        try {
            DB::connection('mysql_target')->statement("DROP TABLE IF EXISTS `{$tableName}`");
            logMessage("  - Dropped table: {$tableName}");
        } catch (\Exception $e) {
            logMessage("  - Failed to drop table {$tableName}: " . $e->getMessage(), true);
        }
    }
    
    // Re-enable foreign key checks
    DB::connection('mysql_target')->statement('SET FOREIGN_KEY_CHECKS=1');
}

// Run Laravel's migrations on MySQL to establish the schema
logMessage("Running migrations to create schema in MySQL...");
try {
    Artisan::call('migrate', [
        '--database' => 'mysql_target',
        '--path' => 'database/migrations',
        '--force' => true,
    ]);
    $migrationOutput = Artisan::output();
    logMessage($migrationOutput);
} catch (\Exception $e) {
    logMessage("Error running migrations: " . $e->getMessage(), true);
    exit(1);
}

// Copy data table by table with enhanced handling
logMessage("Starting data migration from SQLite to MySQL...");

// Track statistics
$stats = [
    'success' => 0,
    'errors' => 0,
    'skipped' => 0,
    'total_records' => 0,
];

// Dictionary of type conversions for problematic fields 
// (expand as needed based on your data)
$typeConversions = [
    // Examples of specific field conversions 
    // 'table.column' => function($value) { return converted_value; }
    'settings.value' => function($value) {
        // Handle JSON strings that might be stored differently
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        }
        return $value;
    },
    // Boolean fields might need special handling
    '.is_active' => function($value) {
        // Convert various boolean representations to 1/0
        if (is_string($value)) {
            $lower = strtolower($value);
            if (in_array($lower, ['true', 'yes', 'y', '1'])) return 1;
            if (in_array($lower, ['false', 'no', 'n', '0'])) return 0;
        }
        return $value ? 1 : 0;
    },
    '.is_combo' => function($value) {
        return $value ? 1 : 0;
    },
    '.is_popular' => function($value) {
        return $value ? 1 : 0;
    },
    '.is_special' => function($value) {
        return $value ? 1 : 0;
    },
];

// Skip these system tables (expand as needed)
$skipTables = [
    'migrations',
    'password_reset_tokens',
    'personal_access_tokens',
    'failed_jobs',
    'cache',
    'sessions',
];

// Tables to migrate in a specific order (optional - helps with foreign key constraints)
$orderedTables = [
    'users',
    'categories',
    'products',
    'toppings',
    'product_toppings',
    'product_extras',
    'combos',
    'combo_products',
    'combo_upsell_product',
    'settings',
    'discounts',
    'orders',
    'order_items',
];

// Build the final table list, respecting order but including all tables
$finalTableList = [];
foreach ($orderedTables as $orderedTable) {
    $finalTableList[] = $orderedTable;
}

// Add any remaining tables not in the ordered list
foreach ($tables as $table) {
    $tableName = $table->name;
    if (!in_array($tableName, $orderedTables) && !in_array($tableName, $skipTables)) {
        $finalTableList[] = $tableName;
    }
}

// Process each table
foreach ($finalTableList as $tableName) {
    // Skip tables in the skip list
    if (in_array($tableName, $skipTables)) {
        logMessage("Skipping system table: {$tableName}");
        $stats['skipped']++;
        continue;
    }

    // Check if the table exists in the MySQL target
    if (!Schema::connection('mysql_target')->hasTable($tableName)) {
        logMessage("Table {$tableName} doesn't exist in MySQL target schema. Skipping.", true);
        $stats['skipped']++;
        continue;
    }
    
    logMessage("Processing table: {$tableName}");
    
    try {
        // Get column information for the table in MySQL
        $columns = Schema::connection('mysql_target')->getColumnListing($tableName);
        
        // Get the data from SQLite
        $rows = DB::connection('sqlite')->table($tableName)->get();
        $count = count($rows);
        $stats['total_records'] += $count;
        
        if ($count > 0) {
            logMessage("  - Found {$count} rows to migrate");
            
            // Disable foreign key checks and unique/auto_increment temporarily
            DB::connection('mysql_target')->statement('SET FOREIGN_KEY_CHECKS=0');
            
            // Process in batches to avoid memory issues
            $batchSize = 100;
            $chunks = array_chunk($rows->toArray(), $batchSize);
            $chunkCount = count($chunks);
            
            logMessage("  - Processing in {$chunkCount} batches of up to {$batchSize} records each");
            
            foreach ($chunks as $index => $chunk) {
                // Convert stdClass objects to arrays with type conversion
                $records = [];
                foreach ($chunk as $row) {
                    $record = [];
                    foreach ((array) $row as $column => $value) {
                        // Skip columns that don't exist in the target table
                        if (!in_array($column, $columns)) {
                            continue;
                        }
                        
                        // Apply type conversions if defined
                        $fullKey = "{$tableName}.{$column}";
                        $partialKey = ".{$column}";
                        
                        if (isset($typeConversions[$fullKey])) {
                            $value = $typeConversions[$fullKey]($value);
                        } elseif (isset($typeConversions[$partialKey])) {
                            $value = $typeConversions[$partialKey]($value);
                        }
                        
                        // Handle NULL values correctly
                        if ($value === null) {
                            $record[$column] = null;
                        } else {
                            $record[$column] = $value;
                        }
                    }
                    $records[] = $record;
                }
                
                // Insert into MySQL
                try {
                    if (!empty($records)) {
                        DB::connection('mysql_target')->table($tableName)->insert($records);
                    }
                    logMessage("  - Batch " . ($index + 1) . "/{$chunkCount} inserted successfully");
                } catch (QueryException $e) {
                    logMessage("  - Error inserting batch " . ($index + 1) . " in table {$tableName}: " . $e->getMessage(), true);
                    $stats['errors']++;
                    
                    // Try one-by-one insertion as fallback
                    logMessage("  - Attempting record-by-record insertion as fallback");
                    foreach ($records as $recordIdx => $record) {
                        try {
                            DB::connection('mysql_target')->table($tableName)->insert([$record]);
                        } catch (QueryException $e2) {
                            logMessage("    - Failed to insert record #{$recordIdx}: " . $e2->getMessage(), true);
                        }
                    }
                }
            }
            
            // Re-enable foreign key checks
            DB::connection('mysql_target')->statement('SET FOREIGN_KEY_CHECKS=1');
            
            // Fix auto-increment for tables with ID columns
            if (in_array('id', $columns)) {
                $maxId = DB::connection('mysql_target')
                    ->table($tableName)
                    ->max('id');
                
                if ($maxId) {
                    $nextId = $maxId + 1;
                    try {
                        DB::connection('mysql_target')
                            ->statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = {$nextId}");
                        logMessage("  - Reset auto-increment for {$tableName} to {$nextId}");
                    } catch (\Exception $e) {
                        logMessage("  - Could not reset auto-increment for {$tableName}: " . $e->getMessage(), true);
                    }
                }
            }
            
            logMessage("  - Successfully migrated data for table: {$tableName}");
            $stats['success']++;
        } else {
            logMessage("  - No data to migrate for table: {$tableName}");
            $stats['success']++;
        }
    } catch (\Exception $e) {
        logMessage("Error processing table {$tableName}: " . $e->getMessage(), true);
        $stats['errors']++;
    }
}

// Calculate elapsed time
$endTime = microtime(true);
$elapsedTime = round($endTime - $startTime, 2);

// Output summary
echo PHP_EOL;
echo "╔═════════════════════════════════════════════════════════╗" . PHP_EOL;
echo "║                MIGRATION SUMMARY                        ║" . PHP_EOL;
echo "╚═════════════════════════════════════════════════════════╝" . PHP_EOL;
echo PHP_EOL;
logMessage("Migration completed in " . formatElapsedTime($elapsedTime));
logMessage("Total tables processed: " . count($finalTableList));
logMessage("  • Successfully migrated: {$stats['success']}");
logMessage("  • Skipped tables: {$stats['skipped']}");
logMessage("  • Tables with errors: {$stats['errors']}");
logMessage("Total records migrated: {$stats['total_records']}");
echo PHP_EOL;

// Post-migration instructions
echo "╔═════════════════════════════════════════════════════════╗" . PHP_EOL;
echo "║                NEXT STEPS                               ║" . PHP_EOL;
echo "╚═════════════════════════════════════════════════════════╝" . PHP_EOL;
echo PHP_EOL;
logMessage("1. Update your .env file with these settings:");
echo "   DB_CONNECTION=mysql" . PHP_EOL;
echo "   DB_HOST=" . $mysqlConfig['host'] . PHP_EOL;
echo "   DB_PORT=" . $mysqlConfig['port'] . PHP_EOL;
echo "   DB_DATABASE=" . $mysqlConfig['database'] . PHP_EOL;
echo "   DB_USERNAME=" . $mysqlConfig['username'] . PHP_EOL;
echo "   DB_PASSWORD=YOUR_PASSWORD" . PHP_EOL;
echo PHP_EOL;
logMessage("2. Clear Laravel's cache to recognize the change:");
echo "   php artisan config:clear" . PHP_EOL;
echo "   php artisan cache:clear" . PHP_EOL;
echo PHP_EOL;
logMessage("3. Test your application with MySQL as the database.");
echo PHP_EOL;