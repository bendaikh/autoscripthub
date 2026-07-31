<?php
// Setup Dodo Payments Database Columns

echo "Setting up Dodo Payments database...\n\n";

// Database configuration - update if needed
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'autoscripthub';

// Connect to database
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected to database successfully.\n\n";

// Step 1: Restore currencies table if missing
echo "Checking currencies table...\n";
$result = $conn->query("SHOW TABLES LIKE 'currencies'");
if ($result->num_rows == 0) {
    echo "Currencies table missing. Restoring from seed file...\n";
    $sql = file_get_contents('app/Seeds/currencies.sql');
    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->next_result());
        echo "✓ Currencies table restored successfully.\n\n";
    } else {
        echo "Error restoring currencies: " . $conn->error . "\n";
    }
} else {
    echo "✓ Currencies table exists.\n\n";
}

// Step 2: Check if dodopayments columns exist
echo "Checking dodopayments columns in additional_settings table...\n";
$result = $conn->query("SHOW COLUMNS FROM additional_settings LIKE 'dodopayments_mode'");

if ($result->num_rows == 0) {
    // Columns don't exist, add them
    echo "Adding dodopayments columns...\n";
    
    $sql = "ALTER TABLE additional_settings 
            ADD COLUMN dodopayments_mode VARCHAR(10) NULL,
            ADD COLUMN dodopayments_api_key TEXT NULL,
            ADD COLUMN dodopayments_business_id TEXT NULL";
    
    if ($conn->query($sql)) {
        echo "✓ Dodopayments columns added successfully.\n\n";
    } else {
        echo "Error adding columns: " . $conn->error . "\n";
        // Try adding them one by one if the combined query fails
        echo "Trying to add columns individually...\n";
        
        $columns = [
            "dodopayments_mode VARCHAR(10) NULL",
            "dodopayments_api_key TEXT NULL", 
            "dodopayments_business_id TEXT NULL"
        ];
        
        foreach ($columns as $col) {
            $colName = explode(' ', $col)[0];
            $checkResult = $conn->query("SHOW COLUMNS FROM additional_settings LIKE '$colName'");
            
            if ($checkResult->num_rows == 0) {
                $sql = "ALTER TABLE additional_settings ADD COLUMN $col";
                if ($conn->query($sql)) {
                    echo "✓ Added column: $colName\n";
                } else {
                    echo "✗ Error adding $colName: " . $conn->error . "\n";
                }
            } else {
                echo "- Column $colName already exists, skipping.\n";
            }
        }
    }
} else {
    echo "✓ Dodopayments columns already exist.\n\n";
}

// Step 3: Mark migration as complete
echo "Checking migration status...\n";
$result = $conn->query("SELECT * FROM migrations WHERE migration = '2025_10_10_000001_add_dodopayments_fields_to_settings'");

if ($result->num_rows == 0) {
    echo "Marking migration as complete...\n";
    $maxBatch = $conn->query("SELECT MAX(batch) as max_batch FROM migrations");
    $batch = $maxBatch->fetch_assoc()['max_batch'] + 1;
    
    $sql = "INSERT INTO migrations (migration, batch) 
            VALUES ('2025_10_10_000001_add_dodopayments_fields_to_settings', $batch)";
    
    if ($conn->query($sql)) {
        echo "✓ Migration marked as complete.\n\n";
    } else {
        echo "✗ Error marking migration: " . $conn->error . "\n";
    }
} else {
    echo "✓ Migration already marked as complete.\n\n";
}

// Verify the setup
echo "Verifying setup...\n";
$result = $conn->query("DESCRIBE additional_settings");
$dodoCols = 0;
while ($row = $result->fetch_assoc()) {
    if (strpos($row['Field'], 'dodopayments_') === 0) {
        echo "✓ Found column: " . $row['Field'] . "\n";
        $dodoCols++;
    }
}

$conn->close();

echo "\n";
echo "================================\n";
echo "SETUP COMPLETE!\n";
echo "================================\n\n";

if ($dodoCols == 3) {
    echo "✓ All 3 dodopayments columns are in place!\n";
    echo "✓ Currencies table is ready!\n";
    echo "✓ Migration is marked as complete!\n\n";
    echo "Next steps:\n";
    echo "1. Go to Admin Panel → Settings → Payment Settings\n";
    echo "2. Scroll to 'Dodo Payments Settings'\n";
    echo "3. Configure your API key and mode\n";
    echo "4. Check 'dodopayments' in payment methods\n";
    echo "5. Test a payment!\n\n";
    echo "You can delete this file (setup_dodopayments.php) after setup.\n";
} else {
    echo "⚠ Warning: Only $dodoCols out of 3 columns were found.\n";
    echo "Please check the error messages above.\n";
}

