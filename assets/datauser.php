<?php
// Start the session
session_name("user_session");
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'You must be logged in to perform this action']);
    exit;
}

include('config.php'); // Include the database connection

// Fetch user ID from the session
$userId = $_SESSION['user_id'];

try {
    // Prepare the SQL statement
    $sql = "SELECT first_name, middle_initial, last_name, contact_number, email, address, zip_code, username 
            FROM users 
            WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind the parameter and execute the query
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch the user data
        if ($user = $result->fetch_assoc()) {
            echo json_encode($user); // Return user data as JSON
        } else {
            echo json_encode(["error" => "User not found"]);
        }

        $stmt->close(); // Close the prepared statement
    } else {
        echo json_encode(["error" => "Failed to prepare database query"]);
    }
} catch (Exception $e) {
    // Handle unexpected errors
    echo json_encode(["error" => "An unexpected error occurred", "details" => $e->getMessage()]);
}

$conn->close(); // Close the database connection
?>
