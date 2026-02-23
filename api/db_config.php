<?php
// Enable error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Get database credentials from environment variables
$server = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');
$port = getenv('DB_PORT') ?: 4000; // TiDB uses port 4000

// Verify all variables are set
if (!$server || !$username || !$password || !$dbname) {
    error_log("Database environment variables missing");
    header('Content-Type: application/json');
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Database configuration error'
    ]));
}

// For TiDB Cloud, we MUST use SSL
$ssl_config = [
    'ssl' => [
        'verify_peer' => true,
        'verify_peer_name' => true,
        'allow_self_signed' => false
    ]
];

// Create connection with SSL
$conn = new mysqli();
$conn->ssl_set(null, null, null, null, null);
$conn->real_connect($server, $username, $password, $dbname, $port, null, MYSQLI_CLIENT_SSL);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    header('Content-Type: application/json');
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

// Set charset
$conn->set_charset("utf8");

error_log("Database connected successfully with SSL");
?>
