<?php
session_name("user_session");
session_start();
include_once "../assets/config.php";

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user ID from session
    $userId = $_SESSION['user_id'];

    // Get the posted data
    $orderItemId = isset($_POST['order_item_id']) ? (int)$_POST['order_item_id'] : 0;
    $newQuantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

    // Check if the input values are valid
    if ($orderItemId > 0) {
        // Check if quantity is 0 and prompt deletion
        if ($newQuantity === 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Quantity is zero. Please press the delete button to remove this item.'
            ]);
            exit; // Stop further execution since we want to prompt for deletion
        }

        // Fetch the product price from the product_items table
        $productQuery = "
            SELECT pi.price 
            FROM order_items oi
            JOIN product_items pi ON oi.product_id = pi.product_id
            WHERE oi.order_item_id = ? AND oi.user_id = ?";
        
        $stmt = $conn->prepare($productQuery);
        $stmt->bind_param("ii", $orderItemId, $userId);
        $stmt->execute();
        $productResult = $stmt->get_result();

        // Check if the order item exists
        if ($productResult->num_rows > 0) {
            $productData = $productResult->fetch_assoc();
            $productPrice = $productData['price'];

            // Calculate the total price
            $totalPrice = $newQuantity * $productPrice;

            // Prepare the SQL statement to update the order item
            $updateQuery = "
                UPDATE order_items 
                SET quantity = ? 
                WHERE order_item_id = ? AND user_id = ?";

            // Prepare the statement
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("iii", $newQuantity, $orderItemId, $userId);
            
            // Execute the statement
            if ($updateStmt->execute()) {
                // Check if any rows were affected
                if ($updateStmt->affected_rows === 0) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'No changes made to the quantity.'
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Database error: ' . $updateStmt->error
                ]);
            }

            $updateStmt->close();
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Order item not found.'
            ]);
        }

        $stmt->close();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid input data.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);
}

$conn->close();
?>
