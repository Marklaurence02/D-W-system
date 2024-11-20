<?php
session_name("user_session");
session_start();
require_once '../assets/config.php'; // Include your database configuration file

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'User not logged in.']));
}

$currentUserId = $_SESSION['user_id'];

function getAssignedStaff($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT assigned_staff_id FROM user_staff_assignments WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc()['assigned_staff_id'] : null;
}

function isStaffOnline($staffId) {
    global $conn;
    $stmt = $conn->prepare("SELECT status FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $staffId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 && $result->fetch_assoc()['status'] === 'online';
}

function saveChatMessage($senderId, $receiverId, $message) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $senderId, $receiverId, $message);
    $stmt->execute();
}

function getChatMessages($userId) {
    global $conn;
    // Fetch messages between the user and the staff assigned to them
    $stmt = $conn->prepare("SELECT sender_id, message, timestamp FROM chat_messages WHERE (sender_id = ? OR receiver_id = ?) ORDER BY timestamp ASC");
    $stmt->bind_param("ii", $userId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = [];

    // If no previous messages, add the welcome message
    if ($result->num_rows === 0) {
        $messages[] = [
            'sender_id' => 0, // 0 represents the virtual assistant
            'sender_role' => 'assistant', // Add role to distinguish assistant
            'message' => 'Welcome to Dine&Watch! I am your Dine&Watch virtual assistant. I\'ll be happy to answer your questions. For an uninterrupted conversation, please ensure that you have a stable internet connection. Please tell me what you would like to know:',
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    // Fetch all messages and assign sender_role
    while ($row = $result->fetch_assoc()) {
        // Get the role of the sender (user or staff)
        $sender_role = ($row['sender_id'] == $userId) ? 'user' : 'assistant'; // Assume other side is the assistant
        $messages[] = [
            'sender_id' => $row['sender_id'],
            'sender_role' => $sender_role, // Add the sender role for distinction
            'message' => $row['message'],
            'timestamp' => $row['timestamp']
        ];
    }
    return $messages;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request = $_POST['message'];
    $staffId = getAssignedStaff($currentUserId);
    if ($staffId) {
        if (isStaffOnline($staffId)) {
            saveChatMessage($currentUserId, $staffId, $request);
            echo json_encode(['response' => "Your message has been sent to our staff."]);
        } else {
            echo json_encode(['response' => "The staff member is currently offline."]);
        }
    } else {
        echo json_encode(['response' => "No staff member is assigned to assist you at the moment."]);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $messages = getChatMessages($currentUserId);
    echo json_encode(['messages' => $messages]);
}
?>
