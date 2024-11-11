<?php
include '../assets/config.php';

session_name("user_session");
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['user_id']) && isset($_GET['receiver'])) {
    $senderId = $_SESSION['user_id'];
    $receiverId = filter_var($_GET['receiver'], FILTER_VALIDATE_INT);

    if ($receiverId === false) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid receiver ID']);
        exit();
    }

    $sql = "SELECT cm.sender_id, cm.receiver_id, cm.message, cm.timestamp, u.first_name 
            FROM chat_messages cm
            JOIN users u ON cm.sender_id = u.user_id
            WHERE (cm.sender_id = ? AND cm.receiver_id = ?)
               OR (cm.sender_id = ? AND cm.receiver_id = ?)
            ORDER BY cm.timestamp DESC
            LIMIT 100";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('iiii', $senderId, $receiverId, $receiverId, $senderId);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $messages = [];

            while ($row = $result->fetch_assoc()) {
                $messages[] = $row;
            }

            $messages = array_reverse($messages);
            echo json_encode($messages);
        } else {
            error_log("Error executing message fetch statement: " . $stmt->error);
            http_response_code(500);
            echo json_encode(['error' => 'Error fetching messages']);
        }

        $stmt->close();
    } else {
        error_log("Error preparing statement: " . $conn->error);
        http_response_code(500);
        echo json_encode(['error' => 'Failed to prepare statement']);
    }
} else {
    http_response_code(401);
    echo json_encode(['error' => 'User not authenticated or no receiver specified']);
}

$conn->close();
?>
