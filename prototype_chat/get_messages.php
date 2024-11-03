<?php
include 'db_connection.php';
header('Content-Type: application/json');

session_start();
if (isset($_SESSION['username'])) {
    $sender = $_SESSION['username'];
    $receiver = htmlspecialchars(trim($_GET['receiver']));

    $stmt = $conn->prepare("SELECT * FROM chat_messages WHERE 
                            (sender = ? AND receiver = ?) OR 
                            (sender = ? AND receiver = ?) 
                            ORDER BY timestamp DESC");
    $stmt->bind_param("ssss", $sender, $receiver, $receiver, $sender);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode($messages);
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
}
?>
