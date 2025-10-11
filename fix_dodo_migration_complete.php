<?php

/**
 * Complete DodoPayments Migration Fix
 * This script fixes the columns and marks the migration as complete
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "=== DodoPayments Migration Fix ===\n\n";
    
    // Step 1: Check current state
    echo "Step 1: Checking current state...\n";
    $columns = [
        'dodopayments_mode' => 'string',
        'dodopayments_api_key' => 'string', 
        'dodopayments_business_id' => 'string'
    ];
    
    $existingColumns = [];
    foreach (array_keys($columns) as $column) {
        if (Schema::hasColumn('additional_settings', $column)) {
            $existingColumns[] = $column;
            echo "  ✓ Column exists: {$column}\n";
        } else {
            echo "  ✗ Column missing: {$column}\n";
        }
    }
    
    // Step 2: Fix columns if needed
    if (count($existingColumns) > 0 && count($existingColumns) < 3) {
        echo "\nStep 2: Removing incomplete columns...\n";
        Schema::table('additional_settings', function ($table) use ($existingColumns) {
            $table->dropColumn($existingColumns);
        });
        echo "  ✓ Removed " . count($existingColumns) . " column(s)\n";
        $existingColumns = [];
    }
    
    // Step 3: Add all columns properly
    if (count($existingColumns) === 0) {
        echo "\nStep 3: Adding all columns with correct types...\n";
        Schema::table('additional_settings', function ($table) {
            $table->string('dodopayments_mode', 10)->nullable()->after('nowpayments_ipn_secret');
            $table->string('dodopayments_api_key', 255)->nullable()->after('dodopayments_mode');
            $table->string('dodopayments_business_id', 255)->nullable()->after('dodopayments_api_key');
        });
        echo "  ✓ All columns added successfully\n";
    } else if (count($existingColumns) === 3) {
        echo "\nStep 3: All columns already exist\n";
        
        // Check if they are the correct type (VARCHAR not TEXT)
        $columnInfo = DB::select("SHOW COLUMNS FROM additional_settings WHERE Field IN ('dodopayments_mode', 'dodopayments_api_key', 'dodopayments_business_id')");
        $needsTypeChange = false;
        
        foreach ($columnInfo as $col) {
            if (stripos($col->Type, 'text') !== false) {
                $needsTypeChange = true;
                echo "  ! Column {$col->Field} is TEXT type, needs to be VARCHAR\n";
            }
        }
        
        if ($needsTypeChange) {
            echo "\nConverting TEXT columns to VARCHAR...\n";
            DB::statement("ALTER TABLE `additional_settings` 
                MODIFY COLUMN `dodopayments_mode` VARCHAR(10) NULL,
                MODIFY COLUMN `dodopayments_api_key` VARCHAR(255) NULL,
                MODIFY COLUMN `dodopayments_business_id` VARCHAR(255) NULL");
            echo "  ✓ Columns converted to VARCHAR\n";
        }
    }
    
    // Step 4: Mark migration as complete
    echo "\nStep 4: Marking migration as complete...\n";
    
    $migrationExists = DB::table('migrations')
        ->where('migration', '2025_10_10_000001_add_dodopayments_fields_to_settings')
        ->exists();
    
    if ($migrationExists) {
        echo "  ✓ Migration already marked as complete\n";
    } else {
        $lastBatch = DB::table('migrations')->max('batch') ?? 0;
        DB::table('migrations')->insert([
            'migration' => '2025_10_10_000001_add_dodopayments_fields_to_settings',
            'batch' => $lastBatch + 1
        ]);
        echo "  ✓ Migration marked as complete (batch " . ($lastBatch + 1) . ")\n";
    }
    
    echo "\n=== SUCCESS ===\n";
    echo "DodoPayments migration is now complete!\n";
    echo "Run 'php artisan migrate:status' to verify.\n\n";
    
} catch (Exception $e) {
    echo "\n=== ERROR ===\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    
    echo "Manual fix - Run these SQL queries:\n\n";
    echo "-- Step 1: Remove any existing columns\n";
    echo "ALTER TABLE `additional_settings` DROP COLUMN IF EXISTS `dodopayments_mode`;\n";
    echo "ALTER TABLE `additional_settings` DROP COLUMN IF EXISTS `dodopayments_api_key`;\n";
    echo "ALTER TABLE `additional_settings` DROP COLUMN IF EXISTS `dodopayments_business_id`;\n\n";
    
    echo "-- Step 2: Add columns with correct type\n";
    echo "ALTER TABLE `additional_settings` ADD `dodopayments_mode` VARCHAR(10) NULL AFTER `nowpayments_ipn_secret`;\n";
    echo "ALTER TABLE `additional_settings` ADD `dodopayments_api_key` VARCHAR(255) NULL AFTER `dodopayments_mode`;\n";
    echo "ALTER TABLE `additional_settings` ADD `dodopayments_business_id` VARCHAR(255) NULL AFTER `dodopayments_api_key`;\n\n";
    
    echo "-- Step 3: Mark migration as complete\n";
    echo "INSERT INTO `migrations` (`migration`, `batch`) VALUES ('2025_10_10_000001_add_dodopayments_fields_to_settings', (SELECT MAX(batch) + 1 FROM (SELECT batch FROM migrations) as temp));\n\n";
    
    exit(1);
}

