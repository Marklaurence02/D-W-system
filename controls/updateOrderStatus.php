<?php
include_once "../assets/config.php";  // Include database configuration

// Get the data from the POST request
$order_id = $_POST['record'];
$new_status = $_POST['new_status'];

// Get user information (you might have these in session)
session_start();
$user_id = $_SESSION['user_id'] ?? null;  // Assuming user_id is stored in session
$username = $_SESSION['username'] ?? 'Unknown User';  // Assuming username is stored in session

// Check if user is logged in
if (!$user_id) {
    echo "error: User not authenticated";
    exit();
}

// SQL query to update the order status
$sql = "UPDATE orders SET status = ? WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $new_status, $order_id);

if ($stmt->execute()) {
    // Log the action in activity_logs
    $action_type = 'Update Status';
    $action_details = "Order ID $order_id status changed to '$new_status' by user ID $user_id (Username: $username).";

    // Insert the action log into the activity_logs table
    $log_query = "INSERT INTO activity_logs (action_by, action_type, action_details) 
                  VALUES (?, ?, ?)";
    $log_stmt = $conn->prepare($log_query);

    if ($log_stmt) {
        $log_stmt->bind_param('iss', $user_id, $action_type, $action_details);

        // Execute the log insert and handle success/failure of the logging
        if ($log_stmt->execute()) {
            echo "success";  // Order status updated and action logged
        } else {
            echo "error: Failed to log action";  // Logging failed
        }

        $log_stmt->close();
    } else {
        echo "error: Failed to prepare log query";  // Log query preparation failed
    }
} else {
    echo "error: Failed to update order status";  // Order update failed
}

$stmt->close();
$conn->close();
?>
