<?php
require_once 'db_config.php';

// Enable error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Log that the script was accessed
error_log("save_qr.php accessed - POST data: " . print_r($_POST, true));

// Check if required parameters are set
if (!isset($_POST['qrcode']) || !isset($_POST['price']) || !isset($_POST['tier'])) {
    error_log("Missing parameters: " . print_r($_POST, true));
    echo "Error: Missing required parameters";
    exit();
}

$qr = $conn->real_escape_string($_POST['qrcode']);
$price = $conn->real_escape_string($_POST['price']);
$tier = $conn->real_escape_string($_POST['tier']);

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

error_log("Inserting into table: $table, column: $qr_column");

// Insert QR code with price and set verified to 'Pending'
$sql = "INSERT INTO $table ($qr_column, price, verified) 
        VALUES ('$qr', '$price', 'Pending')";

if ($conn->query($sql) === TRUE) {
    error_log("Insert successful for QR: $qr");
    echo "Success";
} else {
    error_log("Insert error: " . $conn->error);
    echo "Error: " . $conn->error;
}

$conn->close();
?>
