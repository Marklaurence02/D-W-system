<?php
include_once "../assets/config.php";  // Include the database connection

// Enable error reporting for debugging (you can remove this in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the request method is POST and both category ID and category name are provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id']) && isset($_POST['category_name'])) {
    $categoryID = intval($_POST['category_id']);  // Sanitize category ID
    $categoryName = trim($_POST['category_name']);  // Sanitize and trim category name

    // Check if the category name is empty
    if (empty($categoryName)) {
        echo json_encode(['success' => false, 'message' => 'Category name cannot be empty']);
        exit();
    }

    // Retrieve the logged-in user ID and username from the session
    session_start();
    $userID = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['username'] ?? 'Unknown User';

    if (!$userID) {
        echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
        exit();
    }

    // Prepare SQL statement to update the category in the database
    $query = "UPDATE product_categories SET category_name = ? WHERE category_id = ?";
    $stmt = $conn->prepare($query);

    // Check if the statement was prepared successfully
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Error preparing the SQL statement: ' . $conn->error]);
        exit();
    }

    $stmt->bind_param("si", $categoryName, $categoryID);  // Bind the category name and category ID

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        // Log the action
        $actionType = 'Update Category';
        $actionDetails = "Category (ID: $categoryID) updated by user (ID: $userID, Username: $username) to '$categoryName'.";

        // Insert log into the activity_logs table
        $logQuery = "INSERT INTO activity_logs (action_by, action_type, action_details) VALUES (?, ?, ?)";
        $logStmt = $conn->prepare($logQuery);

        if ($logStmt) {
            $logStmt->bind_param("iss", $userID, $actionType, $actionDetails);

            if ($logStmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Category updated successfully and action logged']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to log the action: ' . $logStmt->error]);
            }

            $logStmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Error preparing log statement: ' . $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update category: ' . $stmt->error]);
    }

    // Close statement
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();
?>
