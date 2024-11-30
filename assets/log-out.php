<?php
session_start(); // Start the session

// Include the database connection configuration
include 'assets/config.php';

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
        // Optional: Log an error if statement preparation fails
        error_log("Failed to prepare logout activity log statement: " . $conn->error);
    }

    // Update user status to 'offline'
    $updateStatusSQL = "UPDATE users SET status = 'offline' WHERE user_id = ?";
    if ($update_stmt = $conn->prepare($updateStatusSQL)) {
        $update_stmt->bind_param('i', $user_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        // Optional: Log an error if the status update fails
        error_log("Failed to update user status to offline: " . $conn->error);
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
