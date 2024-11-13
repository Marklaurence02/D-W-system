<?php
session_name("user_session");
session_start();

header('Content-Type: application/json');

include '../assets/config.php';

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'User not logged in']);
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Connect to the database
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }

    // Query to get assigned staff for the user
    $stmt = $conn->prepare("SELECT assigned_staff_id FROM user_staff_assignments WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $assigned_staff_id = 0; // Default to bot if no staff assigned

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $assigned_staff_id = (int)$row['assigned_staff_id'];
    }

    // Output assigned staff ID
    echo json_encode(['assigned_staff_id' => $assigned_staff_id]);

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['error' => 'An error occurred']);
}
?>
