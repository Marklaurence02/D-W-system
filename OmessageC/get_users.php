<?php
include '../assets/config.php';
session_name("admin_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Start output buffering
ob_start();

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'User not authenticated']);
    exit();
}

$currentUserId = $_SESSION['user_id'];
$currentRole = $_SESSION['role'];

try {
    // Determine which roles to fetch based on current user's role
    $rolesToFetch = [];
    switch ($currentRole) {
        case 'Admin':
            $rolesToFetch = ['Owner', 'Staff'];
            break;
        case 'Owner':
            $rolesToFetch = ['Admin'];
            break;
        case 'Staff':
            $rolesToFetch = ['Admin'];
            break;
        default:
            throw new Exception("Invalid role");
    }

    // Create placeholders for the IN clause
    $rolePlaceholders = str_repeat('?,', count($rolesToFetch) - 1) . '?';

    // Query to get users with their unread message count
    $sql = "SELECT 
                u.user_id,
                u.first_name,
                u.last_name,
                u.role,
                COALESCE(
                    (SELECT COUNT(*) 
                     FROM chat_messages m 
                     WHERE m.sender_id = u.user_id 
                     AND m.receiver_id = ? 
                     AND m.is_read = 0), 0
                ) as unread_count,
                (SELECT MAX(timestamp) 
                 FROM chat_messages 
                 WHERE (sender_id = u.user_id AND receiver_id = ?)
                 OR (sender_id = ? AND receiver_id = u.user_id)
                ) as last_message_time
            FROM users u
            WHERE u.role IN ($rolePlaceholders)
            ORDER BY last_message_time DESC NULLS LAST";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception($conn->error);
    }

    // Create array of parameters for binding
    $params = array_merge(
        [$currentUserId, $currentUserId, $currentUserId], // For the subqueries
        $rolesToFetch // For the IN clause
    );

    // Create types string for bind_param
    $types = str_repeat('s', count($params));

    // Bind parameters
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'user_id' => $row['user_id'],
            'name' => $row['first_name'] . ' ' . $row['last_name'],
            'role' => $row['role'],
            'unread_count' => (int)$row['unread_count'],
            'last_message_time' => $row['last_message_time']
        ];
    }

    // Clear output buffer and send response
    ob_clean();
    echo json_encode([
        'status' => 'success',
        'users' => $users
    ]);

} catch (Exception $e) {
    error_log("Error in get_user.php: " . $e->getMessage());
    http_response_code(500);
    ob_clean();
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch users'
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
    ob_end_flush();
}