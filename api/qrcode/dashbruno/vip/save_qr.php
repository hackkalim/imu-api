<?php
require_once 'db_config.php';

// Check if BOTH qrcode and price are set
if (isset($_POST['qrcode']) && isset($_POST['price'])) {
    $qr = $conn->real_escape_string($_POST['qrcode']);
    $price = $conn->real_escape_string($_POST['price']);
    
    // Inserting QR, Price, and setting the verified column to 'Pending'
    $sql = "INSERT INTO qrcodevip (qrcodevip, price, verified) 
            VALUES ('$qr', '$price', 'Pending')";

    if ($conn->query($sql) === TRUE) {
        echo "Success";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();

?>
