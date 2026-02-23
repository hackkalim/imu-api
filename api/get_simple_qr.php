<?php
// Enable error logging but don't display to output
ini_set('display_errors', 0);
ini_set('log_errors', 1);
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
    require_once 'db_config.php';
    
    // Simple query to get data
    $sql = "SELECT id, qrcodegenerate as qr_code, verified, price 
            FROM qrcodegenerate 
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
    error_log("get_simple_qr.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
