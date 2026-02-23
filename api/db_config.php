<?php
// Enable error reporting for debugging (remove after fixing)
ini_set('display_errors', 0); // Don't display to output
ini_set('log_errors', 1);      // Log to error log
error_reporting(E_ALL);

// Get database credentials from environment variables
$server = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');

// Check if all environment variables are set
if (!$server || !$username || !$password || !$dbname) {
    error_log("Database environment variables missing");
    header('Content-Type: application/json');
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Database configuration error'
    ]));
}

$conn = new mysqli($server, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    header('Content-Type: application/json');
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]));
}

$conn->set_charset("utf8");
?>
