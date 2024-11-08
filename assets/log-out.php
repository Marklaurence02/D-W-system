<?php
session_start(); // Start the session

// Include the database connection configuration
include '../assets/config.php';

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
        // Optional: Log an error or handle the issue gracefully if statement preparation fails
        error_log("Failed to prepare logout activity log statement: " . $conn->error);
    }

    // Clear session variables securely
    $_SESSION = [];
    session_unset(); // Clear session data from the global $_SESSION variable

    // Destroy the session
    session_destroy();

    // Redirect to the home page
    header("Location: ../index.php");
    exit();
} else {
    // If the user is not logged in, redirect directly to the home page
    header("Location: ../index.php");
    exit();
}
?>
