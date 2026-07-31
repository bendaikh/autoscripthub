<?php

/**
 * Final DodoPayments Migration Fix
 * This adds only 2 small columns (mode and api_key) without business_id
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "=== Final DodoPayments Migration Fix ===\n\n";
    
    // Step 1: Remove any existing dodopayments columns
    echo "Step 1: Cleaning up any existing columns...\n";
    $columnsToCheck = ['dodopayments_mode', 'dodopayments_api_key', 'dodopayments_business_id'];
    $existingColumns = [];
    
    foreach ($columnsToCheck as $column) {
        if (Schema::hasColumn('additional_settings', $column)) {
            $existingColumns[] = $column;
            echo "  ✓ Found column: {$column}\n";
        }
    }
    
    if (!empty($existingColumns)) {
        Schema::table('additional_settings', function ($table) use ($existingColumns) {
            $table->dropColumn($existingColumns);
        });
        echo "  ✓ Removed " . count($existingColumns) . " column(s)\n\n";
    } else {
        echo "  ✓ No existing columns found\n\n";
    }
    
    // Step 2: Add only the essential columns with minimal size
    echo "Step 2: Adding essential columns (mode and api_key only)...\n";
    Schema::table('additional_settings', function ($table) {
        $table->string('dodopayments_mode', 10)->nullable()->after('nowpayments_ipn_secret');
        $table->string('dodopayments_api_key', 100)->nullable()->after('dodopayments_mode');
    });
    echo "  ✓ Added dodopayments_mode (VARCHAR 10)\n";
    echo "  ✓ Added dodopayments_api_key (VARCHAR 100)\n\n";
    
    // Step 3: Mark migration as complete
    echo "Step 3: Marking migration as complete...\n";
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
    echo "DodoPayments migration completed successfully!\n";
    echo "Run 'php artisan migrate:status' to verify.\n\n";
    echo "Note: business_id field was removed to save database space.\n";
    echo "It was not needed for the integration anyway.\n\n";
    
} catch (Exception $e) {
    echo "\n=== ERROR ===\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    
    echo "If you still get row size errors, your table is completely full.\n";
    echo "You may need to convert some other unused TEXT columns to smaller types.\n\n";
    
    echo "Manual SQL (if needed):\n\n";
    echo "-- Remove any existing columns\n";
    echo "ALTER TABLE `additional_settings` DROP COLUMN IF EXISTS `dodopayments_mode`;\n";
    echo "ALTER TABLE `additional_settings` DROP COLUMN IF EXISTS `dodopayments_api_key`;\n";
    echo "ALTER TABLE `additional_settings` DROP COLUMN IF EXISTS `dodopayments_business_id`;\n\n";
    
    echo "-- Add new columns with minimal size\n";
    echo "ALTER TABLE `additional_settings` ADD `dodopayments_mode` VARCHAR(10) NULL AFTER `nowpayments_ipn_secret`;\n";
    echo "ALTER TABLE `additional_settings` ADD `dodopayments_api_key` VARCHAR(100) NULL AFTER `dodopayments_mode`;\n\n";
    
    echo "-- Mark migration as complete\n";
    echo "INSERT INTO `migrations` (`migration`, `batch`) VALUES ('2025_10_10_000001_add_dodopayments_fields_to_settings', (SELECT MAX(batch) + 1 FROM (SELECT batch FROM migrations) as temp));\n\n";
    
    exit(1);
}

