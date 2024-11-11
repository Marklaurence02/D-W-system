<?php
session_name("user_session");
session_start();

header('Content-Type: application/json');
include '../assets/config.php';

try {
    if (isset($_SESSION['role'], $_SESSION['user_id'])) {
        $currentRole = $_SESSION['role'];
        $rolesToFetch = [];

        if ($currentRole === 'Admin') {
            $rolesToFetch = ['Owner', 'Staff'];
        } elseif ($currentRole === 'Owner') {
            $rolesToFetch = ['Admin', 'Staff'];
        } elseif ($currentRole === 'Staff') {
            $rolesToFetch = ['Admin'];
        }

        if (!empty($rolesToFetch)) {
            $rolePlaceholders = implode(',', array_fill(0, count($rolesToFetch), '?'));
            $sql = "SELECT user_id, username, role, status FROM users WHERE role IN ($rolePlaceholders)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param(str_repeat('s', count($rolesToFetch)), ...$rolesToFetch);
                $stmt->execute();
                $result = $stmt->get_result();
                $users = $result->fetch_all(MYSQLI_ASSOC);
                echo json_encode($users);
                $stmt->close();
            } else {
                error_log("Database query preparation error: " . $conn->error);
                http_response_code(500);
                echo json_encode(['error' => 'Database query error']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'No users to fetch for your role']);
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
