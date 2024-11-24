<?php
include '../assets/config.php'; // Include database configuration
session_name("user_session");
session_start();
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    $query = "SELECT product_name, price FROM product_items WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
    $stmt->close();
}
?>
