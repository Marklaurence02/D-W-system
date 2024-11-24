<?php
include_once "../assets/config.php";

header('Content-Type: application/json'); // Ensure the response is JSON

// Check if the request method is POST and the category name is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {

    $catname = trim($_POST['category_name']); // Get the category name and trim whitespace
    
    // Check if the category name is empty
    if (empty($catname)) {
        echo json_encode(['success' => false, 'message' => 'Category name cannot be empty']);
        exit;
    }

    // Function to find the lowest available category_id
    function getLowestAvailableID($conn) {
        $query = "SELECT MIN(t1.category_id + 1) AS missing_id
                  FROM product_categories t1
                  LEFT JOIN product_categories t2 ON t1.category_id + 1 = t2.category_id
                  WHERE t2.category_id IS NULL";
        
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['missing_id'];
        }
        return null; // Return null if no missing ID is found
    }

    // Find the missing ID
    $missing_id = getLowestAvailableID($conn);

    // Prepare SQL for category insertion
    if ($missing_id) {
        $insert_query = "INSERT INTO product_categories (category_id, category_name) VALUES ('$missing_id', '$catname')";
    } else {
        // If no missing ID, let MySQL auto-increment handle it
        $insert_query = "INSERT INTO product_categories (category_name) VALUES ('$catname')";
    }

    // Execute the query
    $insert = mysqli_query($conn, $insert_query);

    // Check for errors during insertion
    if (!$insert) {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]); // Return error message from MySQL
    } else {
        echo json_encode(['success' => true, 'message' => 'Category added successfully']);
    }
} else {
    // Handle invalid request method or missing category name
    echo json_encode(['success' => false, 'message' => 'Invalid request or missing category name']);
}
?>
