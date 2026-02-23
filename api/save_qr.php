<?php
// Enable error logging but don't display to output
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Log that the script was accessed
error_log("===== save_qr.php accessed =====");
error_log("POST data: " . print_r($_POST, true));

require_once 'db_config.php';

// Check if ALL required parameters are set
$required_params = ['qrcode', 'price', 'tier'];
$missing_params = [];

foreach ($required_params as $param) {
    if (!isset($_POST[$param]) || empty($_POST[$param])) {
        $missing_params[] = $param;
        error_log("Missing parameter: $param");
    }
}

if (!empty($missing_params)) {
    error_log("ERROR: Missing parameters: " . implode(', ', $missing_params));
    http_response_code(400);
    echo "Error: Missing required parameters: " . implode(', ', $missing_params);
    exit();
}

// Get and sanitize values
$qr = $conn->real_escape_string($_POST['qrcode']);
$price = $conn->real_escape_string($_POST['price']);
$tier = $conn->real_escape_string($_POST['tier']);

error_log("Processing - QR: $qr, Price: $price, Tier: $tier");

// Determine which table and column to use based on tier
$table = '';
$qr_column = '';

if ($tier === 'vip') {
    $table = 'qrcodevip';
    $qr_column = 'qrcodevip';
} elseif ($tier === 'vvip') {
    $table = 'qrcodevvip';
    $qr_column = 'qrcodevvip';
} else {
    // Default to simple
    $table = 'qrcodegenerate';
    $qr_column = 'qrcodegenerate';
}

error_log("Target table: $table, column: $qr_column");

// Check if table exists (optional but helpful for debugging)
$check_table = $conn->query("SHOW TABLES LIKE '$table'");
if ($check_table->num_rows == 0) {
    error_log("ERROR: Table '$table' does not exist");
    http_response_code(500);
    echo "Error: Table '$table' does not exist in database";
    exit();
}

// Insert QR code with price and set verified to 'Pending'
$sql = "INSERT INTO $table ($qr_column, price, verified) 
        VALUES ('$qr', '$price', 'Pending')";

error_log("SQL: $sql");

if ($conn->query($sql) === TRUE) {
    $insert_id = $conn->insert_id;
    error_log("SUCCESS: Inserted with ID: $insert_id");
    echo "Success";
} else {
    error_log("ERROR: " . $conn->error);
    http_response_code(500);
    echo "Error: " . $conn->error;
}

$conn->close();
?>
