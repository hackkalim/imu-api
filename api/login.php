<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log that the script was accessed
error_log("===== login.php accessed =====");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Test database connection first
    require_once 'db_config.php';
    error_log("Database connection successful");
    
    // Simple test response
    echo json_encode([
        'success' => true,
        'message' => 'Login API is working',
        'data' => [
            'user_id' => 1,
            'username' => 'test',
            'session_token' => 'test_token_123'
        ]
    ]);
    
} catch (Exception $e) {
    error_log("ERROR: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
