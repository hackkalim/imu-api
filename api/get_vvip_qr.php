<?php
// Enable detailed error reporting for debugging
ini_set('display_errors', 0); // Don't display to output
ini_set('log_errors', 1);      // Log to error log
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Log that the script was accessed
    error_log("get_vvip_qr.php accessed");
    
    require_once 'db_config.php';
    
    // Check if table exists first
    $check_table = $conn->query("SHOW TABLES LIKE 'qrcodevvip'");
    if ($check_table->num_rows == 0) {
        throw new Exception("Table 'qrcodevvip' does not exist");
    }
    
    $sql = "SELECT id, qrcodevvip as qr_code, verified, price 
            FROM qrcodevvip 
            ORDER BY id DESC 
            LIMIT 50";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id' => (int)$row['id'],
            'qr_code' => $row['qr_code'],
            'verified' => $row['verified'],
            'price' => (float)$row['price']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
    
} catch (Exception $e) {
    error_log("get_vvip_qr.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
