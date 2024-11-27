<?php
include_once "../assets/config.php"; // Ensure correct path to config file

// Assume user_id is stored in session (modify based on your authentication system)
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Function to log user actions in the activity_logs table
function logActivity($conn, $user_id, $action_type, $action_details) {
    $query = "INSERT INTO activity_logs (action_by, action_type, action_details) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("iss", $user_id, $action_type, $action_details);
        $stmt->execute();
        $stmt->close();
    } else {
        error_log("Error logging activity: " . $conn->error);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate the product ID to ensure it's set
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Product ID is missing.']);
        exit;
    }

    // Fetch and sanitize form inputs
    $product_id = intval($_POST['product_id']);
    $product_name = htmlspecialchars($_POST['product_name']);
    $category = htmlspecialchars($_POST['category']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);
    $special_instructions = htmlspecialchars($_POST['special_instructions']);

    // Fetch the existing image path from the database in case no new image is uploaded
    $qry_fetch_image = "SELECT product_image FROM product_items WHERE product_id=?";
    if ($stmt_fetch = $conn->prepare($qry_fetch_image)) {
        $stmt_fetch->bind_param("i", $product_id);
        $stmt_fetch->execute();
        $stmt_fetch->bind_result($existing_image_path);
        $stmt_fetch->fetch();
        $stmt_fetch->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: Unable to fetch current image.']);
        exit;
    }

    // Use the same upload directory as the "add" process
    $upload_dir = "../Uploads/";
    $product_image = $existing_image_path; // Default to the current image path

    // Check if a new image has been uploaded
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) {
        // Get file details
        $name = $_FILES['item_image']['name'];
        $temp = $_FILES['item_image']['tmp_name'];
        $file_size = $_FILES['item_image']['size'];
        $file_type = mime_content_type($temp);

        // File type and size validation
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_extension = pathinfo($name, PATHINFO_EXTENSION);
        if (!in_array($file_type, $allowed_types) || !in_array($file_extension, ['jpeg', 'jpg', 'png', 'gif'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
            exit;
        }

        if ($file_size > 5242880) { // 5MB limit
            echo json_encode(['status' => 'error', 'message' => 'File size exceeds 5MB.']);
            exit;
        }

        // Create a new unique file name to avoid overwriting
        $new_file_name = uniqid() . '_' . basename($name);
        $image_path = $upload_dir . $new_file_name;

        // Remove the old image file if it exists (optional: only if file is not the default placeholder)
        if (file_exists($existing_image_path) && strpos($existing_image_path, 'default-placeholder') === false) {
            unlink($existing_image_path); // Delete the old image file
        }

        // Move the uploaded file to the specified directory
        if (!move_uploaded_file($temp, $image_path)) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to move the uploaded file.']);
            exit;
        }

        // Set the new image path to be updated in the database
        $product_image = $image_path;
    }

    // Ensure all required fields are filled
    if (empty($product_name) || empty($category) || empty($quantity) || empty($price)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields except special instructions are required.']);
        exit;
    }

    // Prepare the SQL statement to update the product
    $qry = "UPDATE product_items SET product_name=?, category=?, quantity=?, price=?, special_instructions=?, product_image=?, updated_at=CURRENT_TIMESTAMP WHERE product_id=?";
    
    if ($stmt = $conn->prepare($qry)) {
        // Bind parameters including the image path
        $stmt->bind_param("ssidssi", $product_name, $category, $quantity, $price, $special_instructions, $product_image, $product_id);

        // Execute the query and check for success
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Product updated successfully.']);

            // Log the activity with 'Update Product' as action_type
            $action_details = "Updated product: " . $product_name . " (Category: " . $category . ")";
            logActivity($conn, $user_id, 'Update Product', $action_details);  // Log the action
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating product: ' . $stmt->error]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: Unable to prepare the statement.']);
    }

    // Close the database connection
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
