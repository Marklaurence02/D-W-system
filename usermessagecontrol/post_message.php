<?php
session_name("user_session");
session_start();

header('Content-Type: application/json');

include '../assets/config.php';

try {
    if (!isset($_SESSION['user_id'], $_POST['message'])) {
        echo json_encode(['error' => 'Unauthorized or missing parameters']);
        exit;
    }

    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver'];
    $message = trim($_POST['message']);

    // Connect to the database
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }

    // Insert the message into the chat_messages table
    $stmt = $conn->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Message sent successfully']);
    } else {
        echo json_encode(['error' => 'Failed to send message']);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['error' => 'An error occurred']);
}
?>
