<?php
include '../assets/config.php';
session_name("user_session");
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['typing'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit();
}

$user_id = $_SESSION['user_id'];
$is_typing = $_POST['typing'] ? 1 : 0;

$sql = "UPDATE users SET typing = ? WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $is_typing, $user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['error' => 'Failed to update typing status']);
}

$stmt->close();
$conn->close();
?>
