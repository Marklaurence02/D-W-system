<?php
include_once "../assets/config.php";  // Include database configuration

$order_id = $_POST['record'];
$new_status = $_POST['new_status'];

// SQL query to update the order status
$sql = "UPDATE orders SET status = ? WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $new_status, $order_id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}

$stmt->close();
$conn->close();
?>
