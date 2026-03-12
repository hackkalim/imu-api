<?php
include 'db_config.php';

header('Content-Type: application/json');

$tier = isset($_GET['tier']) ? $_GET['tier'] : 'simple';
$response = ['total' => 0, 'verified' => 0, 'pending' => 0];

// Map tier to table
$tableMap = [
    'simple' => 'qrcodegenerate',
    'vip' => 'qrcodevip',
    'vvip' => 'qrcodevvip'
];

$table = isset($tableMap[$tier]) ? $tableMap[$tier] : 'qrcodegenerate';

// Check if table exists
$tableCheck = $conn->query("SHOW TABLES LIKE '$table'");
if ($tableCheck->num_rows == 0) {
    echo json_encode($response);
    exit;
}

// Get total count
$totalResult = $conn->query("SELECT COUNT(*) as cnt FROM $table");
if ($totalResult) {
    $row = $totalResult->fetch_assoc();
    $response['total'] = (int)$row['cnt'];
}

// Get verified count
$verifiedResult = $conn->query("SELECT COUNT(*) as cnt FROM $table WHERE verified = 'Verified'");
if ($verifiedResult) {
    $row = $verifiedResult->fetch_assoc();
    $response['verified'] = (int)$row['cnt'];
}

// Get pending count
$pendingResult = $conn->query("SELECT COUNT(*) as cnt FROM $table WHERE verified = 'Pending' OR verified IS NULL OR verified = ''");
if ($pendingResult) {
    $row = $pendingResult->fetch_assoc();
    $response['pending'] = (int)$row['cnt'];
}

// If verified + pending doesn't equal total, adjust pending
if ($response['verified'] + $response['pending'] != $response['total']) {
    $response['pending'] = $response['total'] - $response['verified'];
}

echo json_encode($response);
$conn->close();
?>