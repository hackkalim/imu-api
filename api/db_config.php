<?php
// Turn off display errors
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Get database credentials from environment variables
$server = getenv('DB_HOST') ?: 'sql204.infinityfree.com';
$username = getenv('DB_USER') ?: 'if0_41188377';
$password = getenv('DB_PASSWORD') ?: 'jrP3XVBEjDQBq8S';
$dbname = getenv('DB_NAME') ?: 'if0_41188377_qrcodegenerate';

$conn = new mysqli($server, $username, $password, $dbname);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]));
}

$conn->set_charset("utf8");
?>