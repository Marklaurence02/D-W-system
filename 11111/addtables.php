<?php
include_once "../assets/config.php"; // Ensure correct DB connection

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if form is submitted with "upload" key
if (isset($_POST['upload'])) {
    // Sanitize and capture form inputs
    $table_number = intval($_POST['table_number']);
    $seating_capacity = intval($_POST['seating_capacity']);
    $area = $_POST['area']; // Should be 'Indoor' or 'Outdoor'

    // Validate required fields
    if (empty($table_number) || empty($seating_capacity) || empty($area)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    // Check if the table number already exists in the same area
    $checkQuery = "SELECT * FROM tables WHERE table_number = ? AND area = ?";
    $stmt = $conn->prepare($checkQuery);
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Error preparing the SQL statement: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("is", $table_number, $area);
    $stmt->execute();
    $result = $stmt->get_result();

    // If table number exists in the same area, return an error message
    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Table number already exists in the $area area."]);
        exit;
    }

    // Insert the table data into the `tables` table
    $query = "INSERT INTO tables (table_number, seating_capacity, area) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Error preparing the SQL statement: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("iis", $table_number, $seating_capacity, $area);
    if ($stmt->execute()) {
        // Get the inserted table ID
        $table_id = $stmt->insert_id;
    } else {
        echo json_encode(["status" => "error", "message" => "Error inserting table: " . $stmt->error]);
        exit;
    }

    // Array to hold file upload paths
    $image_paths = [];
    $upload_dir = "../uploads/"; // Ensure this directory exists with write permissions

    // Handle multiple image uploads (front, back, left, right)
    $image_fields = ['front_image', 'back_image', 'left_image', 'right_image'];
    $image_positions = ['front view', 'back view', 'left view', 'right view'];

    foreach ($image_fields as $index => $field) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
            $temp = $_FILES[$field]['tmp_name'];
            $name = $_FILES[$field]['name'];
            $file_size = $_FILES[$field]['size'];
            $file_type = mime_content_type($temp);

            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file_type, $allowed_types)) {
                echo json_encode(["status" => "error", "message" => "Invalid file type for $field. Only JPG, PNG, and GIF are allowed."]);
                exit;
            }

            // Validate file size (max 5MB)
            if ($file_size > 5242880) { // 5MB limit
                echo json_encode(["status" => "error", "message" => "File size for $field is too large. Max size is 5MB."]);
                exit;
            }

            // Create unique filename
            $image_path = $upload_dir . uniqid() . '_' . $name;

            // Move file to uploads directory
            if (!move_uploaded_file($temp, $image_path)) {
                echo json_encode(["status" => "error", "message" => "Failed to move the uploaded file for $field."]);
                exit;
            }

            // Insert image into table_images with correct position
            $query = "INSERT INTO table_images (table_id, image_path, position) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                echo json_encode(["status" => "error", "message" => "Error preparing the SQL statement for images: " . $conn->error]);
                exit;
            }
            $position = $image_positions[$index]; // Match position to field
            $stmt->bind_param("iss", $table_id, $image_path, $position);
            if (!$stmt->execute()) {
                echo json_encode(["status" => "error", "message" => "Error inserting image path: " . $stmt->error]);
                exit;
            }

            // Collect image paths for success message (optional)
            $image_paths[] = $image_path;
        }
    }

    echo json_encode(["status" => "success", "message" => "Table and images added successfully.", "images" => $image_paths]);

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}

$conn->close();
