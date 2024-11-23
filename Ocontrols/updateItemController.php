<?php
include_once "../assets/config.php"; // Ensure correct path to config file

session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Function to log user actions
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

$response = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        $response['status'] = 'error';
        $response['message'] = 'Product ID is missing.';
        echo json_encode($response);
        exit;
    }

    $product_id = intval($_POST['product_id']);
    $product_name = htmlspecialchars($_POST['product_name']);
    $category_id = intval($_POST['category_id']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);
    $special_instructions = htmlspecialchars($_POST['special_instructions']);

    // Fetch current image
    $qry_fetch_image = "SELECT product_image FROM product_items WHERE product_id=?";
    if ($stmt_fetch = $conn->prepare($qry_fetch_image)) {
        $stmt_fetch->bind_param("i", $product_id);
        $stmt_fetch->execute();
        $stmt_fetch->bind_result($existing_image_path);
        $stmt_fetch->fetch();
        $stmt_fetch->close();
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Database error: Unable to fetch current image.';
        echo json_encode($response);
        exit;
    }

    // Set image upload directory
    $upload_dir = "../Uploads/";
    $product_image = $existing_image_path;

    // Handle new image upload if provided
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) {
        $name = $_FILES['item_image']['name'];
        $temp = $_FILES['item_image']['tmp_name'];
        $file_size = $_FILES['item_image']['size'];
        $file_type = mime_content_type($temp);

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_extension = pathinfo($name, PATHINFO_EXTENSION);
        if (!in_array($file_type, $allowed_types) || !in_array($file_extension, ['jpeg', 'jpg', 'png', 'gif'])) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
            echo json_encode($response);
            exit;
        }

        if ($file_size > 5242880) { // 5MB limit
            $response['status'] = 'error';
            $response['message'] = 'File size exceeds 5MB.';
            echo json_encode($response);
            exit;
        }

        $new_file_name = uniqid() . '_' . basename($name);
        $image_path = $upload_dir . $new_file_name;

        // Remove old image if necessary
        if (file_exists($existing_image_path) && strpos($existing_image_path, 'default-placeholder') === false) {
            unlink($existing_image_path);
        }

        if (!move_uploaded_file($temp, $image_path)) {
            $response['status'] = 'error';
            $response['message'] = 'Failed to move the uploaded file.';
            echo json_encode($response);
            exit;
        }

        $product_image = $image_path;
    }

    if (empty($product_name) || empty($category_id) || empty($quantity) || empty($price)) {
        $response['status'] = 'error';
        $response['message'] = 'All fields except special instructions are required.';
        echo json_encode($response);
        exit;
    }

    // Update the product record
    $qry = "UPDATE product_items SET product_name=?, category_id=?, quantity=?, price=?, special_instructions=?, product_image=?, updated_at=CURRENT_TIMESTAMP WHERE product_id=?";
    
    if ($stmt = $conn->prepare($qry)) {
        $stmt->bind_param("siiissi", $product_name, $category_id, $quantity, $price, $special_instructions, $product_image, $product_id);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Product updated successfully.';
            $action_details = "Updated product: " . $product_name . " (Category ID: " . $category_id . ")";
            logActivity($conn, $user_id, 'Update Product', $action_details);
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error updating product: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Database error: Unable to prepare the statement.';
    }

    echo json_encode($response);
    $conn->close();
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
}
