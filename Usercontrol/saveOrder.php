<?php
session_start();
include_once "../assets/config.php"; // Ensure correct path to your config file

if (isset($_POST['product_id'], $_POST['quantity'], $_POST['price'], $_POST['user_id'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $user_id = (int)$_POST['user_id'];

    // Retrieve or create `order_id` here (currently hardcoded for example)
    $order_id = 1; // Replace with dynamic logic for actual application

    // Check if the product already exists in the order_items table
    $checkQuery = "SELECT order_item_id, quantity, totalprice FROM order_items WHERE order_id = ? AND user_id = ? AND product_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("iii", $order_id, $user_id, $product_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Update existing order item
        $existingOrder = $checkResult->fetch_assoc();
        $existingQuantity = (int)$existingOrder['quantity'];
        $existingTotalPrice = (float)$existingOrder['totalprice'];

        $newQuantity = $existingQuantity + $quantity;
        $pricePerItem = $existingTotalPrice / $existingQuantity;
        $newTotalPrice = $pricePerItem * $newQuantity;

        $updateQuery = "UPDATE order_items SET quantity = ?, totalprice = ? WHERE order_item_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("idi", $newQuantity, $newTotalPrice, $existingOrder['order_item_id']);

        if ($updateStmt->execute()) {
            echo json_encode(["status" => "update", "message" => "Order updated successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: Could not update the order."]);
        }
        $updateStmt->close();
    } else {
        // Insert new order item
        $insertStmt = $conn->prepare("INSERT INTO order_items (order_id, user_id, product_id, quantity, totalprice) VALUES (?, ?, ?, ?, ?)");
        if ($insertStmt) {
            $insertStmt->bind_param("iiidi", $order_id, $user_id, $product_id, $quantity, $price);

            if ($insertStmt->execute()) {
                echo json_encode(["status" => "add", "message" => "Order added successfully!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error: Could not confirm the order."]);
            }
            $insertStmt->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Error preparing insert statement."]);
        }
    }
    $checkStmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Error: Invalid data."]);
}

$conn->close();
?>
