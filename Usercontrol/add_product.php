<?php
include '../assets/config.php';  // Include DB connection

// Check if required POST data is provided
if (isset($_POST['order_id'], $_POST['product_id'], $_POST['quantity'])) {
    $order_id = $_POST['order_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Fetch product details
    $stmt = $conn->prepare("SELECT product_name, price FROM product_items WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        $product = $stmt->get_result()->fetch_assoc();

        if ($product) {
            // Calculate total price
            $total_price = $product['price'] * $quantity;

            // Update order details and total amount
            $stmt = $conn->prepare("UPDATE orders SET order_details = CONCAT(order_details, ?), total_amount = total_amount + ? WHERE order_id = ?");
            $order_details = " Product Name: " . $product['product_name'] . ", Quantity: $quantity, Price: $total_price";
            $stmt->bind_param("sdi", $order_details, $total_price, $order_id);

            if ($stmt->execute()) {
                // Insert into order_items table
                $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, totalprice) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $total_price);

                if ($stmt->execute()) {
                    echo "Product added successfully!";
                } else {
                    echo "Error inserting into order_items table: " . $stmt->error;
                }
            } else {
                echo "Error updating order details: " . $stmt->error;
            }
        } else {
            echo "Product not found.";
        }
    } else {
        echo "Error fetching product details: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
} else {
    echo "Missing required data.";
}

// Close database connection
$conn->close();
?>
