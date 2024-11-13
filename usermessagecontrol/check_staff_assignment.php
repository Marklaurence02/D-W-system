<?php
session_name("user_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Retrieve the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

include '../assets/config.php'; // Ensure this path is correct

// Establish the MySQLi connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check for connection errors
if ($conn->connect_error) {
    error_log("Database connection error: " . $conn->connect_error);
    echo json_encode(['error' => 'A database connection error occurred. Please try again later.']);
    exit;
}

try {
    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT assigned_staff_id FROM user_staff_assignments WHERE user_id = ?");
    
    if ($stmt) {
        // Bind the parameter and execute the statement
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Get the result
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Fetch assigned staff ID
            $row = $result->fetch_assoc();
            echo json_encode(['assigned_staff_id' => (int)$row['assigned_staff_id']]);
        } else {
            // No staff assigned, fall back to bot with ID 0
            echo json_encode(['assigned_staff_id' => 0]);
        }
        
        // Close the statement
        $stmt->close();
    } else {
        // Log and return an error if the statement preparation fails
        error_log("Database statement preparation error: " . $conn->error);
        echo json_encode(['error' => 'Database query error. Please try again later.']);
    }
} catch (Exception $e) {
    // Log any unexpected errors
    error_log("Unexpected error: " . $e->getMessage());
    echo json_encode(['error' => 'An unexpected error occurred. Please try again later.']);
} finally {
    // Close the database connection
    $conn->close();
}
?>
