<?php
// Check if the necessary session data is available to proceed
if (isset($_SESSION['user_id'])) {
    // Set a unique session name based on the user's ID
    session_name("session_" . md5($_SESSION['user_id']));
}
session_start(); // Start the session with the user-specific session name

// Include the database connection configuration
include 'config.php';

// Check if the user is logged in with all necessary session variables
if (isset($_SESSION['username'], $_SESSION['user_id'], $_SESSION['role'])) {
    // Retrieve user details from the session
    $username = $_SESSION['username'];
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    // 1. Update the user's status to 'offline'
    $status_sql = "UPDATE users SET status = 'offline' WHERE user_id = ?";
    if ($status_stmt = $conn->prepare($status_sql)) {
        $status_stmt->bind_param('i', $user_id);
        $status_stmt->execute();
        $status_stmt->close(); // Close the statement
    } else {
        error_log("Error preparing status update query: " . $conn->error);
    }

    // 2. Log the logout activity
    $action_type = "Logout";
    $action_details = "$username logged out";
    $log_sql = "INSERT INTO activity_logs (action_by, action_type, action_details) VALUES (?, ?, ?)";
    if ($log_stmt = $conn->prepare($log_sql)) {
        $log_stmt->bind_param('iss', $user_id, $action_type, $action_details);
        $log_stmt->execute();
        $log_stmt->close(); // Close the statement
    } else {
        error_log("Error preparing log insertion query: " . $conn->error);
    }

    // 3. Clear all session variables
    $_SESSION = [];

    // 4. Destroy the session to fully log out the user
    session_destroy();

    // 5. Redirect based on user role
    // Admin and Staff users are redirected to the admin sign-in page, others to the main index page
    if (in_array($role, ['Owner', 'Admin', 'Staff'])) {
        header("Location: ../ad-sign-in.php");
    } else {
        header("Location: ../index.php");
    }
    exit();
} else {
    // If user is not logged in, redirect to the login page directly
    header("Location: ../ad-sign-in.php");
    exit();
}
