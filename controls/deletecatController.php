<?php
include_once "../assets/config.php";
session_start();

header('Content-Type: application/json'); // Always return JSON responses

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    $categoryID = intval($_POST['category_id']);
    $userID = $_SESSION['user_id'] ?? null;

    if (!$userID) {
        echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
        exit;
    }

    // Check if the category is assigned to any product
    $checkQuery = "SELECT COUNT(*) AS product_count FROM product_items WHERE category_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    if ($checkStmt) {
        $checkStmt->bind_param("i", $categoryID);
        $checkStmt->execute();
        $checkStmt->bind_result($productCount);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($productCount > 0) {
            echo json_encode(['success' => false, 'message' => "Cannot delete category. It is assigned to $productCount product(s)."]);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error while checking product assignments.']);
        exit;
    }

    // Begin transaction for atomic operation
    $conn->begin_transaction();

    try {
        $deleteQuery = "DELETE FROM product_categories WHERE category_id = ?";
        $stmt = $conn->prepare($deleteQuery);

        if ($stmt) {
            $stmt->bind_param("i", $categoryID);

            if ($stmt->execute()) {
                // Log the action (optional)
                echo json_encode(['success' => true, 'message' => 'Category deleted successfully.']);
                $conn->commit();
            } else {
                throw new Exception('Error deleting category: ' . $stmt->error);
            }

            $stmt->close();
        } else {
            throw new Exception('Error preparing delete statement: ' . $conn->error);
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request or missing parameters.']);
}

$conn->close();
?>
