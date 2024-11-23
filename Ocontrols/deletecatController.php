<?php
include_once "../assets/config.php";  // Include database config

header('Content-Type: application/json');  // Return JSON responses
session_start();  // Start the session to access user data

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    $categoryID = intval($_POST['category_id']);  // Sanitize the category ID
    $userID = $_SESSION['user_id'] ?? null;  // Retrieve user ID from session

    if (!$userID) {
        echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
        exit;
    }

    // Fetch the username of the user performing the action
    $userQuery = "SELECT username FROM users WHERE user_id = ?";
    $userStmt = $conn->prepare($userQuery);
    if ($userStmt) {
        $userStmt->bind_param("i", $userID);
        $userStmt->execute();
        $userStmt->bind_result($username);

        if (!$userStmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
            $userStmt->close();
            exit;
        }
        $userStmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error while fetching username.']);
        exit;
    }

    // Begin transaction for atomic operation
    $conn->begin_transaction();

    try {
        // SQL query to delete the category
        $deleteQuery = "DELETE FROM product_categories WHERE category_id = ?";
        $stmt = $conn->prepare($deleteQuery);

        if ($stmt) {
            $stmt->bind_param("i", $categoryID);

            if ($stmt->execute()) {
                // Log the action with the username included in action details
                $actionType = 'Delete Category';
                $actionDetails = "Category with ID $categoryID was deleted by user '$username' (ID: $userID).";

                $logQuery = "INSERT INTO activity_logs (action_by, action_type, action_details) 
                             VALUES (?, ?, ?)";
                $logStmt = $conn->prepare($logQuery);

                if ($logStmt) {
                    $logStmt->bind_param("iss", $userID, $actionType, $actionDetails);

                    if ($logStmt->execute()) {
                        // Commit transaction
                        $conn->commit();
                        echo json_encode(['success' => true, 'message' => 'Category deleted and action logged successfully']);
                    } else {
                        throw new Exception('Failed to log action: ' . $logStmt->error);
                    }

                    $logStmt->close();
                } else {
                    throw new Exception('Error preparing log statement: ' . $conn->error);
                }
            } else {
                throw new Exception('Error deleting category: ' . $stmt->error);
            }

            $stmt->close();
        } else {
            throw new Exception('Error preparing delete statement: ' . $conn->error);
        }
    } catch (Exception $e) {
        $conn->rollback();  // Rollback transaction on error
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request or missing parameters.']);
}

$conn->close();
?>
