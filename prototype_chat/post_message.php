<?php
include 'db_connection.php';
header('Content-Type: application/json');

session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['username'])) {
        $sender = $_SESSION['username']; // The sender is the logged-in user
        $receiver = htmlspecialchars(trim($_POST['receiver']));
        $message = htmlspecialchars(trim($_POST['message']));

        $stmt = $conn->prepare("INSERT INTO chat_messages (sender, receiver, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $sender, $receiver, $message);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Message sent.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send message.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
    }
}
?>
