<?php
$conn = new mysqli('localhost', 'root', '', 'autoscripthub');

echo "Optimizing dodopayments columns for row size limit...\n\n";

// Step 1: Check what we have
$result = $conn->query("SHOW COLUMNS FROM additional_settings LIKE 'dodopayments_%'");
echo "Current columns:\n";
while($row = $result->fetch_assoc()) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
}
echo "\n";

// Step 2: Try to add business_id with VARCHAR instead of TEXT
$check = $conn->query("SHOW COLUMNS FROM additional_settings LIKE 'dodopayments_business_id'");
if($check->num_rows == 0) {
    echo "Attempting to add dodopayments_business_id as VARCHAR(100)...\n";
    $sql = "ALTER TABLE additional_settings ADD COLUMN dodopayments_business_id VARCHAR(100) NULL";
    
    if($conn->query($sql)) {
        echo "✓ Success! Added dodopayments_business_id\n\n";
    } else {
        echo "✗ Failed with VARCHAR(100). Error: " . $conn->error . "\n";
        echo "Trying with VARCHAR(50)...\n";
        
        $sql = "ALTER TABLE additional_settings ADD COLUMN dodopayments_business_id VARCHAR(50) NULL";
        if($conn->query($sql)) {
            echo "✓ Success! Added dodopayments_business_id as VARCHAR(50)\n\n";
        } else {
            echo "✗ Still failed: " . $conn->error . "\n\n";
        }
    }
} else {
    echo "✓ dodopayments_business_id already exists\n\n";
}

// Final verification
echo "=================================\n";
echo "FINAL STATUS:\n";
echo "=================================\n\n";

$result = $conn->query("SHOW COLUMNS FROM additional_settings LIKE 'dodopayments_%'");
$count = 0;
while($row = $result->fetch_assoc()) {
    echo "✓ " . $row['Field'] . " - " . $row['Type'] . "\n";
    $count++;
}

echo "\n";
if($count == 3) {
    echo "🎉 SUCCESS! All 3 dodopayments columns are ready!\n\n";
    echo "Columns added:\n";
    echo "1. dodopayments_mode - For test/live mode\n";
    echo "2. dodopayments_api_key - For API authentication\n";
    echo "3. dodopayments_business_id - For multi-business support\n\n";
    echo "Next: Go to Admin Panel → Settings → Payment Settings\n";
} else {
    echo "⚠ Only $count out of 3 columns were added.\n";
    echo "You may need to manually add the missing columns.\n";
}

$conn->close();

