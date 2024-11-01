<?php
session_start();
include_once "../assets/config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $data = json_decode(file_get_contents("php://input"), true); // Decode JSON input
    $orderItemId = intval($data['order_item_id']); // Get and sanitize order_item_id

    // Prepare the delete query
    $deleteQuery = "DELETE FROM order_items WHERE order_item_id = ? AND user_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("ii", $orderItemId, $userId);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Item deleted successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete item."]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
