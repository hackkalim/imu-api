<?php
error_log("===== db_config.php loaded =====");

// Get database credentials from environment variables
$server = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');

error_log("DB_HOST: " . ($server ?: 'NOT SET'));
error_log("DB_USER: " . ($username ?: 'NOT SET'));
error_log("DB_PASSWORD: " . ($password ? '********' : 'NOT SET'));
error_log("DB_NAME: " . ($dbname ?: 'NOT SET'));

if (!$server || !$username || !$password || !$dbname) {
    error_log("ERROR: Database environment variables missing");
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

error_log("Database connected successfully");
$conn->set_charset("utf8");
?>
