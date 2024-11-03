<?php
session_start();
include 'db_connection.php';
header('Content-Type: application/json');

// Check if the user is already logged in by user ID
if (isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You are already logged in.']);
    exit; // Stop further execution
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id']; // Store user ID in the session
            $_SESSION['username'] = $row['username']; // Optionally store username as well

            $update_stmt = $conn->prepare("UPDATE users SET is_online = 1 WHERE id = ?");
            $update_stmt->bind_param("i", $row['id']);
            $update_stmt->execute();

            echo json_encode(['status' => 'success', 'message' => 'Login successful!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid password.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
    }
    $stmt->close();
}
?>
