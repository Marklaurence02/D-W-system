<?php
session_name("user_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User  not logged in']);
    exit;
}

$currentUserId = $_SESSION['user_id'];
$receiverId = isset($_GET['receiver']) ? (int)$_GET['receiver'] : null;

if ($receiverId === null) {
    echo json_encode(['error' => 'No receiver specified']);
    exit;
}

include '../assets/config.php'; // Adjust the path as necessary

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE (sender_id = :sender AND receiver_id = :receiver) OR (sender_id = :receiver AND receiver_id = :sender) ORDER BY timestamp ASC");
    $stmt->execute(['sender' => $currentUserId, 'receiver' => $receiverId]);
    
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($messages);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>