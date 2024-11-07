<?php
session_start();
header('Content-Type: application/json');
include '../assets/config.php';

try {
    // Check if user is authenticated
    if (isset($_SESSION['role'], $_SESSION['user_id'])) {
        $currentRole = $_SESSION['role'];
        $rolesToFetch = [];

        // Debug: Log the session role to check if it's set correctly
        error_log("Session role: " . $currentRole);

        // Determine roles to fetch based on user's role
        if ($currentRole === 'Admin') {
            $rolesToFetch = ['Owner', 'Staff'];
        } elseif ($currentRole === 'Owner') {
            $rolesToFetch = ['Admin', 'Staff'];
        } elseif ($currentRole === 'Staff') {
            $rolesToFetch = ['Admin'];
        }

        // Debug: Log the roles to fetch based on the session role
        error_log("Roles to fetch: " . json_encode($rolesToFetch));

        if (!empty($rolesToFetch)) {
            // Prepare SQL query with placeholders for roles
            $rolePlaceholders = implode(',', array_fill(0, count($rolesToFetch), '?'));
            $sql = "SELECT user_id, username, role, status FROM users WHERE role IN ($rolePlaceholders)";
            
            // Debug: Log the final SQL query to check if it's correctly formed
            error_log("SQL Query: $sql");

            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param(str_repeat('s', count($rolesToFetch)), ...$rolesToFetch);
                $stmt->execute();
                $result = $stmt->get_result();
                $users = $result->fetch_all(MYSQLI_ASSOC);
                echo json_encode($users);
                $stmt->close();
            } else {
                // Database preparation error
                error_log("Database query preparation error: " . $conn->error);
                http_response_code(500);
                echo json_encode(['error' => 'Database query error']);
            }
        } else {
            // No roles to fetch based on user's role
            http_response_code(404);
            echo json_encode(['error' => 'No users to fetch for your role']);
        }
    } else {
        // User not authenticated
        http_response_code(401);
        echo json_encode(['error' => 'User not authenticated']);
    }
} catch (Exception $e) {
    // Catch unexpected exceptions and log them
    error_log("Exception occurred: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An unexpected error occurred']);
}

// Close the database connection if it exists
if (isset($conn) && $conn) {
    $conn->close();
}
