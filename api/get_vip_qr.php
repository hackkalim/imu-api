<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    require_once 'db_config.php';
    
    $sql = "SELECT id, qrcodevip as qr_code, verified, price 
            FROM qrcodevip 
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
    error_log("get_vip_qr.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error'
    ]);
}
?>
