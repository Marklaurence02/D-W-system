<?php
 session_name("owner_session");
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
    $check_sql = "SELECT user_id FROM users WHERE user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    if ($check_stmt === false) {
        error_log("MySQL prepare error: " . $conn->error);
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
        exit;
    }
    $check_stmt->bind_param("i", $user_id);  // This is the target user to be deleted
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        $check_stmt->close();
        exit;
    }
    $check_stmt->close();

    // Proceed with deleting the target user if the logged-in user's password is verified
    $delete_sql = "DELETE FROM users WHERE user_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    if ($delete_stmt === false) {
        error_log("MySQL prepare error: " . $conn->error);
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
        exit;
    }

    $delete_stmt->bind_param("i", $user_id);  // Delete the target user
    if ($delete_stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'User deleted successfully.']);
    } else {
        error_log("Execute error: " . $delete_stmt->error);
        echo json_encode(['status' => 'error', 'message' => 'Error deleting the user.']);
    }

    $delete_stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
