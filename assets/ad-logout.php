<?php
session_start();

// Include the database connection configuration
include 'config.php';

// Check if user is logged in
if (isset($_SESSION['user_id'], $_SESSION['session_token'])) {
    $user_id = $_SESSION['user_id'];
    $session_token = $_SESSION['session_token'];

    // Invalidate the session
    $delete_sql = "DELETE FROM sessions WHERE user_id = ? AND session_token = ?";
    $stmt = $conn->prepare($delete_sql);
    if ($stmt) {
        $stmt->bind_param('is', $user_id, $session_token);
        $stmt->execute();
        $stmt->close();
    }

    // Log activity
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];

        // Create the log action details
        $action_details = $username . " logged out"; // Concatenate and store in a variable

        // Prepare and bind parameters for the log entry
        $log_sql = "INSERT INTO activity_logs (action_by, action_type, action_details) VALUES (?, 'Logout', ?)";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->bind_param('is', $user_id, $action_details); // Bind the variable $action_details
        $log_stmt->execute();
        $log_stmt->close();
    }

    // Clear and destroy session
    $_SESSION = [];
    session_unset();
    session_destroy();

    // Redirect to login page
    header("Location: ../ad-sign-in.php");
    exit();
} else {
    header("Location: ../ad-sign-in.php");
    exit();
}
?>
