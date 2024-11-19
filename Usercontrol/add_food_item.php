<?php
// Include database connection
include('assets/config.php');

// Check if the form data is received
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $products = json_decode($_POST['products']); // Decoding the JSON array of product IDs
    $order_details = $_POST['order_details'];
    $total_amount = $_POST['total_amount'];

    // Insert the order into the `orders` table
    $sql = "INSERT INTO orders (user_id, order_details, total_amount, status)
            VALUES ('$user_id', '$order_details', '$total_amount', 'Pending')";

    if (mysqli_query($conn, $sql)) {
        $order_id = mysqli_insert_id($conn); // Get the ID of the newly inserted order

        // Insert each product into the `order_items` table (assuming you have this table)
        foreach ($products as $product_id) {
            $sql_item = "INSERT INTO order_items (order_id, product_id)
                         VALUES ('$order_id', '$product_id')";
            mysqli_query($conn, $sql_item);
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
