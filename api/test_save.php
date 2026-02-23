<?php
// Enable error display for testing
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Test Save Debug</h1>";
echo "<pre>";

echo "POST data received:\n";
print_r($_POST);

echo "\n\nGET data received:\n";
print_r($_GET);

echo "\n\nServer variables:\n";
echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "\n\nAttempting database connection...\n";
    
    require_once 'db_config.php';
    
    echo "Database connected successfully!\n";
    echo "Server info: " . $conn->host_info . "\n";
    
    if (isset($_POST['qrcode']) && isset($_POST['price'])) {
        $qr = $conn->real_escape_string($_POST['qrcode']);
        $price = $conn->real_escape_string($_POST['price']);
        
        echo "\nInserting: QR=$qr, Price=$price\n";
        
        $sql = "INSERT INTO qrcodegenerate (qrcodegenerate, price, verified) 
                VALUES ('$qr', '$price', 'Pending')";
        
        echo "SQL: $sql\n";
        
        if ($conn->query($sql) === TRUE) {
            echo "SUCCESS: Inserted with ID: " . $conn->insert_id;
        } else {
            echo "ERROR: " . $conn->error;
        }
    } else {
        echo "ERROR: Missing qrcode or price in POST data";
    }
}

echo "</pre>";
?>
