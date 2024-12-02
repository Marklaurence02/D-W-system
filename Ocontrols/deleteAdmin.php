<?php

session_start();
include_once "../assets/config.php";  // Include your DB connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to perform this action.']);
    exit;
}

$logged_in_user_id = $_SESSION['user_id'];  // The currently logged-in user (e.g., Admin performing the deletion)
$role = $_SESSION['role'];  // The role of the logged-in user (e.g., Admin)
$username = $_SESSION['username'] ?? 'User';  // The username of the logged-in user

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'], $_POST['user_password'])) {
    $user_id = intval($_POST['user_id']);
    $user_password = $_POST['user_password'];

    // Retrieve the password hash for the logged-in user (the one performing the deletion)
    $password_sql = "SELECT password_hash FROM users WHERE user_id = ?";
    $password_stmt = $conn->prepare($password_sql);
    if ($password_stmt === false) {
        error_log("MySQL prepare error: " . $conn->error);
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
        exit;
    }

    $password_stmt->bind_param("i", $logged_in_user_id);  // Use the logged-in user's ID
    $password_stmt->execute();
    $password_stmt->bind_result($hashed_password);
    
    if (!$password_stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Logged-in user not found.']);
        $password_stmt->close();
        exit;
    }
    $password_stmt->close();

    // Verify the provided password matches the logged-in user's password
    if (!password_verify($user_password, $hashed_password)) {
        echo json_encode(['status' => 'error', 'message' => 'Incorrect password.']);
        exit;
    }

    // Check if the target user (the one to be deleted) exists
    $check_sql = "SELECT username FROM users WHERE user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    if ($check_stmt === false) {
        error_log("MySQL prepare error: " . $conn->error);
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
        exit;
    }
    $check_stmt->bind_param("i", $user_id);  // This is the target user to be deleted
    $check_stmt->execute();
    $check_stmt->bind_result($target_username);

    if (!$check_stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        $check_stmt->close();
        exit;
    }
    $check_stmt->close();

    // Proceed with deleting the target user if the logged-in user's password is verified
    $conn->begin_transaction();

    try {
        // Delete assignments where the user is the assigned staff
        $delete_assigned_staff_sql = "DELETE FROM user_staff_assignments WHERE assigned_staff_id = ?";
        $delete_assigned_staff_stmt = $conn->prepare($delete_assigned_staff_sql);
        if ($delete_assigned_staff_stmt === false) {
            throw new Exception("Database error: " . $conn->error);
        }

        $delete_assigned_staff_stmt->bind_param("i", $user_id);
        if (!$delete_assigned_staff_stmt->execute()) {
            throw new Exception("Error deleting assigned staff: " . $delete_assigned_staff_stmt->error);
        }

        $delete_assigned_staff_stmt->close();

        // Delete assignments for the target user
        $delete_assignments_sql = "DELETE FROM user_staff_assignments WHERE user_id = ?";
        $delete_assignments_stmt = $conn->prepare($delete_assignments_sql);
        if ($delete_assignments_stmt === false) {
            throw new Exception("Database error: " . $conn->error);
        }

        $delete_assignments_stmt->bind_param("i", $user_id);
        if (!$delete_assignments_stmt->execute()) {
            throw new Exception("Error deleting user assignments: " . $delete_assignments_stmt->error);
        }

        $delete_assignments_stmt->close();

        // Proceed with deleting the target user
        $delete_sql = "DELETE FROM users WHERE user_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        if ($delete_stmt === false) {
            throw new Exception("Database error: " . $conn->error);
        }

        $delete_stmt->bind_param("i", $user_id);  // Delete the target user
        if (!$delete_stmt->execute()) {
            throw new Exception("Error deleting the user: " . $delete_stmt->error);
        }

        $delete_stmt->close();

        // Log the action in the activity_logs table
        $action_type = 'Delete Admin';
        $action_details = "Admin '$target_username' (ID: $user_id) was deleted by user '$username' (ID: $logged_in_user_id).";
        $log_query = "INSERT INTO activity_logs (action_by, action_type, action_details) 
                      VALUES (?, ?, ?)";
        $log_stmt = $conn->prepare($log_query);

        if ($log_stmt === false) {
            throw new Exception("Database error: " . $conn->error);
        }

        $log_stmt->bind_param("iss", $logged_in_user_id, $action_type, $action_details);
        if (!$log_stmt->execute()) {
            throw new Exception("Failed to log activity: " . $log_stmt->error);
        }

        $log_stmt->close();

        // Commit the transaction
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'User and assignments deleted, action logged successfully.']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    } finally {
        $conn->close();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
