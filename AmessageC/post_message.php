<?php
session_start();
include 'assets/config.php';

if (!isset($_SESSION['user_id'])) {
    exit(json_encode(['error' => 'Unauthorized']));
}

$data = json_decode(file_get_contents('php://input'), true);
$senderId = $_SESSION['user_id'];
$receiverId = $data['receiver_id'];
$message = $data['message'];

$sql = "INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $senderId, $receiverId, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to send message']);
}

