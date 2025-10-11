<?php
$conn = new mysqli('localhost', 'root', '', 'autoscripthub');

echo "Checking dodopayments columns...\n\n";

$result = $conn->query('SHOW COLUMNS FROM additional_settings');
$existing = [];

while($row = $result->fetch_assoc()) {
    if(strpos($row['Field'], 'dodopayments_') === 0) {
        $existing[] = $row['Field'];
        echo "✓ Found: " . $row['Field'] . "\n";
    }
}

echo "\n";

$needed = [
    'dodopayments_mode' => 'VARCHAR(10)',
    'dodopayments_api_key' => 'TEXT',
    'dodopayments_business_id' => 'TEXT'
];

foreach($needed as $col => $type) {
    if(!in_array($col, $existing)) {
        echo "Adding missing column: $col ($type)...\n";
        $sql = "ALTER TABLE additional_settings ADD COLUMN $col $type NULL";
        if($conn->query($sql)) {
            echo "✓ Added successfully!\n\n";
        } else {
            echo "✗ Error: " . $conn->error . "\n\n";
        }
    }
}

echo "Final check:\n";
$result = $conn->query('SHOW COLUMNS FROM additional_settings');
$count = 0;
while($row = $result->fetch_assoc()) {
    if(strpos($row['Field'], 'dodopayments_') === 0) {
        echo "✓ " . $row['Field'] . " (" . $row['Type'] . ")\n";
        $count++;
    }
}

echo "\n";
if($count == 3) {
    echo "SUCCESS! All 3 dodopayments columns are ready! 🎉\n";
} else {
    echo "Status: $count out of 3 columns found.\n";
}

$conn->close();

