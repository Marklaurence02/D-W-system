<?php
include_once "../assets/config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['action'])) {
    $productId = intval($_POST['product_id']);
    $newStatus = $_POST['action'] === 'deactivate' ? 'inactive' : 'active';

    $sql = "UPDATE product_items SET status = ? WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $newStatus, $productId);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        http_response_code(500);
        echo "Failed to update product status.";
    }

    $stmt->close();
}
$conn->close();
?>
