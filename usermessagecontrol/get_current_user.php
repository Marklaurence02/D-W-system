<?php
session_name("user_session");
session_start();

header('Content-Type: application/json');

include_once '../assets/config.php';

try {
    // Check if the session has the necessary data
    if (isset($_SESSION['username'], $_SESSION['role'], $_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // Prepare and execute the query to retrieve user status
        $status_sql = "SELECT status FROM users WHERE user_id = ?";
        $status_stmt = $conn->prepare($status_sql);

        if ($status_stmt) {
            $status_stmt->bind_param('i', $user_id);
            $status_stmt->execute();
            $status_result = $status_stmt->get_result();

            if ($status_result->num_rows > 0) {
                $row = $status_result->fetch_assoc();
                echo json_encode([
                    'username' => $_SESSION['username'],
                    'role' => $_SESSION['role'],
                    'user_id' => $_SESSION['user_id'],
                    'status' => $row['status']
                ]);
            } else {
                error_log("User status not found for user_id: $user_id");
                http_response_code(404);
                echo json_encode(['error' => 'User status not found']);
            }
            $status_stmt->close();
        } else {
            error_log("Database query preparation error: " . $conn->error);
            http_response_code(500);
            echo json_encode(['error' => 'Database query error']);
        }
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'User not authenticated']);
    }
} catch (Exception $e) {
    error_log("Exception occurred: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An unexpected error occurred']);
}

if (isset($conn) && $conn) {
    $conn->close();
}
?>
