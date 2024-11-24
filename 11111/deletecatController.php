<?php
include_once "../assets/config.php";  // Include database config

header('Content-Type: application/json');  // Return JSON responses

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    $categoryID = intval($_POST['category_id']);  // Sanitize the category ID

    // SQL query to delete the category
    $deleteQuery = "DELETE FROM product_categories WHERE category_id = ?";
    $stmt = $conn->prepare($deleteQuery);

    if ($stmt) {
        $stmt->bind_param("i", $categoryID);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting category: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing SQL statement: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request or missing parameters.']);
}

$conn->close();
?>
