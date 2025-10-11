<?php

/**
 * Fix DodoPayments Migration Issue
 * This script removes the partially added columns so the migration can run cleanly
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "Checking for existing DodoPayments columns in additional_settings table...\n\n";
    
    $columns = ['dodopayments_mode', 'dodopayments_api_key', 'dodopayments_business_id'];
    $columnsToRemove = [];
    
    // Check which columns exist
    foreach ($columns as $column) {
        if (Schema::hasColumn('additional_settings', $column)) {
            $columnsToRemove[] = $column;
            echo "✓ Found column: {$column}\n";
        }
    }
    
    if (empty($columnsToRemove)) {
        echo "\nNo DodoPayments columns found. You can run 'php artisan migrate' now.\n";
        exit(0);
    }
    
    echo "\n" . count($columnsToRemove) . " column(s) need to be removed.\n";
    echo "Removing columns...\n\n";
    
    // Remove the columns
    Schema::table('additional_settings', function ($table) use ($columnsToRemove) {
        $table->dropColumn($columnsToRemove);
    });
    
    echo "✓ Successfully removed columns: " . implode(', ', $columnsToRemove) . "\n";
    echo "\nNow you can run: php artisan migrate\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nAlternative: Run this SQL query manually in your database:\n";
    echo "ALTER TABLE `additional_settings` DROP COLUMN IF EXISTS `dodopayments_mode`, DROP COLUMN IF EXISTS `dodopayments_api_key`, DROP COLUMN IF EXISTS `dodopayments_business_id`;\n";
    exit(1);
}

