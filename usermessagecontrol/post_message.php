<?php
include '../assets/config.php';
session_name("user_session");
session_start();


header('Content-Type: application/json');

if (!isset($_SESSION['user_id'], $_POST['message'])) {
    echo json_encode(['error' => 'Unauthorized or missing parameters']);
    exit();
}

$user_id = $_SESSION['user_id'];
$message = trim($_POST['message']);

// Check if the user has an assigned staff
$assigned_staff_query = "SELECT staff_id FROM user_staff_assignments WHERE user_id = ?";
$stmt = $conn->prepare($assigned_staff_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode(['error' => 'No assigned staff']);
    exit();
}

$staff_id = $row['staff_id'];

// Inside post_message.php, if the assigned staff is 'bot'
if ($staff_id === 'bot') {
    // Handle bot response if assigned to bot
    $bot_reply = "Thank you for your message. Our team will reach out to you soon.";
    $bot_message_query = "INSERT INTO chat_messages (sender_id, receiver_id, message, timestamp) VALUES (NULL, ?, ?, NOW())";
    $stmt = $conn->prepare($bot_message_query);
    $stmt->bind_param("is", $user_id, $bot_reply);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Bot has replied to your message.']);
    } else {
        echo json_encode(['error' => 'Failed to send bot message']);
    }
}

$conn->close();
?>
