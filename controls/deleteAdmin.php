<?php

session_start();
include_once "../assets/config.php";  // Include your DB connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to perform this action.']);
    exit;
}

$logged_in_user_id = $_SESSION['user_id'];  // The currently logged-in user (e.g., Admin performing the action)
$role = $_SESSION['role'];  // The role of the logged-in user (e.g., Admin)
$username = $_SESSION['username'] ?? 'User';  // The username of the logged-in user

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'], $_POST['user_password'], $_POST['action'])) {
    $user_id = intval($_POST['user_id']);
    $user_password = $_POST['user_password'];
    $action = $_POST['action'];  // 'activate' or 'deactivate'

    // Retrieve the password hash for the logged-in user (the one performing the action)
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

    // Check if the target user (the one to be activated/deactivated) exists
    $check_sql = "SELECT username FROM users WHERE user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    if ($check_stmt === false) {
        error_log("MySQL prepare error: " . $conn->error);
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
        exit;
    }
    $check_stmt->bind_param("i", $user_id);  // This is the target user
    $check_stmt->execute();
    $check_stmt->bind_result($target_username);

    if (!$check_stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        $check_stmt->close();
        exit;
    }
    $check_stmt->close();

    // Determine the new account status
    $new_status = ($action === 'activate') ? 'active' : 'inactive';

    // Proceed with updating the account status
    $conn->begin_transaction();

    try {
        $update_sql = "UPDATE users SET account_status = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        if ($update_stmt === false) {
            throw new Exception("Database error: " . $conn->error);
        }

        $update_stmt->bind_param("si", $new_status, $user_id);  // Update the target user's status
        if (!$update_stmt->execute()) {
            throw new Exception("Error updating the user status: " . $update_stmt->error);
        }

        $update_stmt->close();

        // Log the action in the activity_logs table
        $action_type = ucfirst($action) . ' Admin';
        $action_details = "Admin '$target_username' (ID: $user_id) was $action by user '$username' (ID: $logged_in_user_id).";
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
        echo json_encode(['status' => 'success', 'message' => "User $action and action logged successfully."]);
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
