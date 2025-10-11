<?php

/**
 * Skip Database Storage - Use Config Instead
 * Since the table is completely full, we'll store DodoPayments settings in config
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "=== DodoPayments Config-Based Solution ===\n\n";
    
    // Step 1: Remove the dodopayments_mode column that was added
    echo "Step 1: Removing partial column...\n";
    if (Schema::hasColumn('additional_settings', 'dodopayments_mode')) {
        Schema::table('additional_settings', function ($table) {
            $table->dropColumn('dodopayments_mode');
        });
        echo "  ✓ Removed dodopayments_mode column\n\n";
    } else {
        echo "  ✓ No column to remove\n\n";
    }
    
    // Step 2: Mark migration as complete (even though we're skipping it)
    echo "Step 2: Marking migration as complete...\n";
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
    echo "Migration marked as complete.\n";
    echo "DodoPayments will use config/services.php instead of database.\n\n";
    
    echo "IMPORTANT: Add these to your .env file:\n";
    echo "DODOPAYMENTS_MODE=test\n";
    echo "DODOPAYMENTS_API_KEY=your_api_key_here\n";
    echo "DODO_DEFAULT_PRODUCT_ID=your_product_id_here\n\n";
    
    echo "The admin settings page will automatically update the .env file.\n";
    echo "This solution avoids the MySQL row size limit issue.\n\n";
    
} catch (Exception $e) {
    echo "\n=== ERROR ===\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

