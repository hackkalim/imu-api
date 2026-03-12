<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'db_config.php';

// Get QR code from request
$input = json_decode(file_get_contents('php://input'), true);
$qr_code = $input['qr_code'] ?? '';

if (empty($qr_code)) {
    echo json_encode([
        'success' => false,
        'message' => 'QR code is required',
        'status' => 'NOT_FOUND'
    ]);
    exit();
}

// Determine which table based on QR code prefix
$table = '';
$qr_column = '';

// Check for VVIP first (starts with "vv")
if (strpos($qr_code, 'vv') === 0) {
    $table = 'qrcodevvip';
    $qr_column = 'qrcodevvip';
} 
// Check for VIP (starts with "v" but not "vv")
elseif (strpos($qr_code, 'v') === 0) {
    $table = 'qrcodevip';
    $qr_column = 'qrcodevip';
} 
// Simple (no prefix)
else {
    $table = 'qrcodegenerate';
    $qr_column = 'qrcodegenerate';
}

// Check if QR code exists
$check_sql = "SELECT id, verified, price, entry FROM $table WHERE $qr_column = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("s", $qr_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Not found',
        'status' => 'NOT_FOUND'
    ]);
    exit();
}

$row = $result->fetch_assoc();

// Check if the ticket is verified (status = 'Verified')
if (strtolower($row['verified']) !== 'verified') {
    // Ticket is still Pending
    echo json_encode([
        'success' => false,
        'message' => 'Ticket not verified yet',
        'status' => 'NOT_VERIFIED'
    ]);
    exit();
}

// Ticket is verified, now check entry status
if ($row['entry'] == 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Already entered',
        'status' => 'ALREADY_ENTERED'
    ]);
    exit();
}

// Update entry to 1 (mark as entered)
$update_sql = "UPDATE $table SET entry = 1 WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("i", $row['id']);
$update_stmt->execute();

if ($update_stmt->affected_rows > 0) {
    echo json_encode([
        'success' => true,
        'message' => 'Entry recorded',
        'status' => 'ENTRY_OK',
        'data' => [
            'id' => $row['id'],
            'price' => $row['price']
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Update failed',
        'status' => 'ERROR'
    ]);
}

$conn->close();
?>