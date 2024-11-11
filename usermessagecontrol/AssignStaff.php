<?php
include '../assets/config.php';
session_start();

header('Content-Type: application/json');

try {
    // Step 1: Check if the user is logged in and get the user ID
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User is not logged in.");
    }

    $userId = $_SESSION['user_id'];
    $botId = 0; // ID for the bot if no staff is available

    // Step 2: Check if the user already has an assigned staff in `user_staff_assignments`
    $checkAssignmentQuery = "
        SELECT assigned_staff_id 
        FROM user_staff_assignments 
        WHERE user_id = ?
        LIMIT 1;
    ";
    $checkStmt = $conn->prepare($checkAssignmentQuery);
    $checkStmt->bind_param('i', $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // User already has an assigned staff, return this staff ID
        $assignment = $checkResult->fetch_assoc();
        $assignedStaffId = $assignment['assigned_staff_id'];
        echo json_encode(['assigned_staff_id' => $assignedStaffId]);
        exit;
    }

    // Step 3: If no staff is assigned, find the staff with the fewest assignments
    $findStaffQuery = "
        SELECT u.user_id AS staff_id, COUNT(usa.user_id) AS assigned_count
        FROM users u
        LEFT JOIN user_staff_assignments usa ON u.user_id = usa.assigned_staff_id
        WHERE u.role = 'Staff'
        GROUP BY u.user_id
        ORDER BY assigned_count ASC, u.user_id ASC
        LIMIT 1;
    ";
    
    $findStaffStmt = $conn->prepare($findStaffQuery);
    $findStaffStmt->execute();
    $staffResult = $findStaffStmt->get_result();
    $staff = $staffResult->fetch_assoc();

    if ($staff) {
        $assignedStaffId = $staff['staff_id'];

        // Step 4: Assign this staff to the user in `user_staff_assignments`
        $assignQuery = "
            INSERT INTO user_staff_assignments (user_id, assigned_staff_id) 
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE assigned_staff_id = VALUES(assigned_staff_id)
        ";
        
        $assignStmt = $conn->prepare($assignQuery);
        $assignStmt->bind_param('ii', $userId, $assignedStaffId);
        $assignStmt->execute();
    } else {
        // No staff available, fallback to bot
        $assignedStaffId = $botId;
    }

    // Step 5: Return the assigned staff ID or bot ID
    echo json_encode(['assigned_staff_id' => $assignedStaffId]);

} catch (Exception $e) {
    error_log("Error in staff assignment: " . $e->getMessage());
    echo json_encode(['error' => 'Could not assign staff.']);
}
?>
