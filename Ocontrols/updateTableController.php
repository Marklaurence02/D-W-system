<?php
include_once "../assets/config.php";

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_name("owner_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
    // Sanitize and capture form inputs
    $table_id = intval($_POST['table_id']);
    $table_number = intval($_POST['table_number']);
    $seating_capacity = intval($_POST['seating_capacity']);
    $area = htmlspecialchars($_POST['area']);
    $is_available = intval($_POST['is_available']);

    // Prepare SQL statement to update the table data
    $updateQuery = "UPDATE tables SET table_number=?, seating_capacity=?, area=?, is_available=? WHERE table_id=?";
    $stmt = $conn->prepare($updateQuery);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Error preparing the SQL statement: ' . $conn->error]);
        exit();
    }

    $stmt->bind_param("iisii", $table_number, $seating_capacity, $area, $is_available, $table_id);
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Error executing the query: ' . $stmt->error]);
        exit();
    }

    // Image upload positions
    $positions = [
        'new_image_back_view' => 'back view', 
        'new_image_left_view' => 'left view', 
        'new_image_right_view' => 'right view', 
        'new_image_front_view' => 'front view'
    ];

    $upload_dir = "../uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);  // Create the uploads directory if it doesn't exist
    }

    foreach ($positions as $file_key => $position) {
        if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
            $file_name = uniqid() . '_' . basename($_FILES[$file_key]['name']);
            $target_file = $upload_dir . $file_name;

            $file_type = mime_content_type($_FILES[$file_key]["tmp_name"]);
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

            if (in_array($file_type, $allowed_types)) {
                if (move_uploaded_file($_FILES[$file_key]["tmp_name"], $target_file)) {
                    // Check if an image for this position already exists
                    $checkQuery = "SELECT image_id FROM table_images WHERE table_id = ? AND position = ?";
                    $stmt = $conn->prepare($checkQuery);
                    $stmt->bind_param("is", $table_id, $position);
                    $stmt->execute();
                    $stmt->store_result();

                    if ($stmt->num_rows > 0) {
                        // If image exists, update it
                        $updateImageQuery = "UPDATE table_images SET image_path = ? WHERE table_id = ? AND position = ?";
                        $stmt = $conn->prepare($updateImageQuery);
                        $stmt->bind_param("sis", $target_file, $table_id, $position);
                    } else {
                        // Insert new image if none exists
                        $insertImageQuery = "INSERT INTO table_images (table_id, image_path, position) VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($insertImageQuery);
                        $stmt->bind_param("iss", $table_id, $target_file, $position);
                    }

                    // Execute the query for image insertion or update
                    if (!$stmt->execute()) {
                        echo json_encode(['status' => 'error', 'message' => 'Error executing image query: ' . $stmt->error]);
                        exit();
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error: Failed to move the uploaded file.']);
                    exit();
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid image type. Only JPG, PNG, and GIF are allowed.']);
                exit();
            }
        }
    }

    // Log the activity for updating a table
    $action_details = "Updated table ID: $table_id with table number: $table_number";
    logActivity($conn, $user_id, 'Update Table', $action_details);

    // Send a success response
    echo json_encode(['status' => 'success', 'message' => 'Table updated successfully.']);
    $stmt->close();
    $conn->close();
}
