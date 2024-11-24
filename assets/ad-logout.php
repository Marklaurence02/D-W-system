<?php
session_start();

// Include the database connection configuration
include 'config.php';

// Check if user is logged in
if (isset($_SESSION['user_id'], $_SESSION['session_token'])) {
    $user_id = $_SESSION['user_id'];
    $session_token = $_SESSION['session_token'];

    // Invalidate the session in the database
    $delete_sql = "DELETE FROM sessions WHERE user_id = ? AND session_token = ?";
    $stmt = $conn->prepare($delete_sql);
    if ($stmt) {
        $stmt->bind_param('is', $user_id, $session_token);
        $stmt->execute();
        $stmt->close();
    } else {
        error_log("Error deleting session from database: " . $conn->error);
    }

    // Log activity
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $action_details = $username . " logged out";

        $log_sql = "INSERT INTO activity_logs (action_by, action_type, action_details) VALUES (?, 'Logout', ?)";
        $log_stmt = $conn->prepare($log_sql);
        if ($log_stmt) {
            $log_stmt->bind_param('is', $user_id, $action_details);
            $log_stmt->execute();
            $log_stmt->close();
        } else {
            error_log("Error logging logout activity: " . $conn->error);
        }
    }

    // Update user status to 'offline' in the database
    $updateStatusSQL = "UPDATE users SET status = 'offline' WHERE user_id = ?";
    if ($update_stmt = $conn->prepare($updateStatusSQL)) {
        $update_stmt->bind_param('i', $user_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        error_log("Failed to update user status to offline: " . $conn->error);
    }

    // Clear and destroy session
    $_SESSION = [];
    session_unset();
    session_destroy();

    // Delete the session cookie for additional security
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/'); // Set the cookie expiration time to past
    }

    // Redirect to login page
    header("Location: ../ad-sign-in.php");
    exit();
} else {
    // If the user is not logged in, redirect to the login page
    header("Location: ../ad-sign-in.php");
    exit();
}
?>
