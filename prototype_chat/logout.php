<?php
session_start();
include 'db_connection.php';

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $stmt = $conn->prepare("UPDATE users SET is_online = 0 WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    session_destroy();
    echo json_encode(['status' => 'success', 'message' => 'Logged out successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
}
?>
