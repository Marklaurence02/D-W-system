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

    if (empty($categoryName)) {
        echo json_encode(['success' => false, 'message' => 'Category name cannot be empty']);
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
        echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to execute statement: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();
?>
