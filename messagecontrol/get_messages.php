<?php
include '../assets/config.php';

// Start the session if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Check if the user is logged in and if a receiver ID is provided
if (isset($_SESSION['user_id']) && isset($_GET['receiver'])) {
    $senderId = $_SESSION['user_id'];
    $receiverId = filter_var($_GET['receiver'], FILTER_VALIDATE_INT); // Validate receiver ID as an integer

    // If receiver ID is invalid, return a 400 error
    if ($receiverId === false) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid receiver ID']);
        exit();
    }

    // Prepare the SQL statement to fetch messages
    $sql = "SELECT cm.sender_id, cm.receiver_id, cm.message, cm.timestamp, u.first_name 
            FROM chat_messages cm
            JOIN users u ON cm.sender_id = u.user_id
            WHERE (cm.sender_id = ? AND cm.receiver_id = ?)
               OR (cm.sender_id = ? AND cm.receiver_id = ?)
            ORDER BY cm.timestamp DESC
            LIMIT 100"; // Limit to the last 100 messages for performance
    $stmt = $conn->prepare($sql);

    // Check if the statement preparation was successful
    if ($stmt) {
        $stmt->bind_param('iiii', $senderId, $receiverId, $receiverId, $senderId);

        // Execute the statement and check if successful
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $messages = [];

            // Fetch the result as an associative array
            while ($row = $result->fetch_assoc()) {
                $messages[] = $row;
            }

            // Reverse the array so that the newest messages are at the end
            $messages = array_reverse($messages);

            // Output the messages in JSON format
            echo json_encode($messages);
        } else {
            // Log the error message if statement execution fails
            error_log("Error executing message fetch statement: " . $stmt->error);
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Error fetching messages']);
        }

        $stmt->close();
    } else {
        // Log preparation errors
        error_log("Error preparing statement: " . $conn->error);
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Failed to prepare statement']);
    }
} else {
    // Return a 401 error if the user is not authenticated or receiver ID is missing
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'User not authenticated or no receiver specified']);
}

// Close the database connection
$conn->close();
