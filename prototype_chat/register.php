<?php
include 'db_connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Registration successful!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Registration failed.']);
    }
    $stmt->close();
}
?>
