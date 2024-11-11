<?php
// Assuming your database connection is included here
include '../assets/config.php';

// Get the user ID from the request
$user_id = $_GET['user_id'] ?? null;

if ($user_id) {
    // Query to check if the user has an assigned staff
    $stmt = $conn->prepare("SELECT * FROM user_staff_assignments WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Staff is assigned to the user
        echo json_encode(['status' => 'staff_assigned']);
    } else {
        // No staff assigned
        echo json_encode(['status' => 'no_staff']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'User ID is missing']);
}
?>
