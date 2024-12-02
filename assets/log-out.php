<?php
session_name("user_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Include the database connection configuration
include 'config.php';

// Check if the user is logged in
if (isset($_SESSION['username']) && isset($_SESSION['user_id'])) {
    // Capture user details from the session
    $username = $_SESSION['username'];
    $user_id = $_SESSION['user_id'];

    // Log the logout activity
    $action_type = "Logout";
    $action_details = "$username logged out";

    // Prepare and execute the logout log query
    if ($log_stmt = $conn->prepare("INSERT INTO activity_logs (action_by, action_type, action_details) VALUES (?, ?, ?)")) {
        $log_stmt->bind_param('iss', $user_id, $action_type, $action_details);
        $log_stmt->execute();
        $log_stmt->close();
    } else {
        error_log("Failed to prepare logout activity log statement: " . $conn->error);
    }

    // Update user status to 'offline'
    $updateStatusSQL = "UPDATE users SET status = 'offline' WHERE user_id = ?";
    if ($update_stmt = $conn->prepare($updateStatusSQL)) {
        $update_stmt->bind_param('i', $user_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        error_log("Failed to update user status to offline: " . $conn->error);
    }

    // Delete user-related data from order_items
    $deleteOrderItemsSQL = "DELETE FROM order_items WHERE user_id = ?";
    if ($delete_order_stmt = $conn->prepare($deleteOrderItemsSQL)) {
        $delete_order_stmt->bind_param('i', $user_id);
        $delete_order_stmt->execute();
        $delete_order_stmt->close();
    } else {
        error_log("Failed to delete order items: " . $conn->error);
    }

    // Delete user-related data from data_reservations
    $deleteReservationsSQL = "DELETE FROM data_reservations WHERE user_id = ?";
    if ($delete_reservation_stmt = $conn->prepare($deleteReservationsSQL)) {
        $delete_reservation_stmt->bind_param('i', $user_id);
        $delete_reservation_stmt->execute();
        $delete_reservation_stmt->close();
    } else {
        error_log("Failed to delete reservations: " . $conn->error);
    }

    // Clear session variables securely
    $_SESSION = []; // Unset all session variables
    session_unset(); // Remove all session variables
    session_destroy(); // Destroy the session

    // Delete the session cookie (for added security)
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/'); // Set the cookie expiration time to past
    }

    // Redirect to the home page
    header("Location: ../login.php");
    exit();
} else {
    // If the user is not logged in, redirect directly to the home page
    header("Location: ../login.php");
    exit();
}
?>
