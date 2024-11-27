<?php
include_once "../assets/config.php";

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Assume user_id is stored in session (modify based on your authentication system)
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Function to find the lowest available ID
function getLowestAvailableID($conn) {
    // Query to find gaps in the product_id sequence
    $query = "SELECT MIN(t1.product_id + 1) AS missing_id 
              FROM product_items t1 
              LEFT JOIN product_items t2 ON t1.product_id + 1 = t2.product_id 
              WHERE t2.product_id IS NULL";
    
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row['missing_id']) {
            return $row['missing_id'];  // Return the first missing ID
        }
    }
    // If no gaps are found, return NULL
    return NULL;
}

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

// Insert a new product
if (isset($_POST['upload'])) {
    // Sanitize and capture form inputs
    $item_name = htmlspecialchars($_POST['item_name']);
    $item_type = htmlspecialchars($_POST['item_type']);
    $stock = intval($_POST['stock']);
    $price = floatval($_POST['price']);
    $special_instructions = htmlspecialchars($_POST['special_instructions']);
    
    // Check if there's a gap in the product IDs
    $missing_id = getLowestAvailableID($conn);

    // File upload handling
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) {
        $name = $_FILES['item_image']['name'];
        $temp = $_FILES['item_image']['tmp_name'];
        $file_size = $_FILES['item_image']['size'];
        $file_type = mime_content_type($temp);

        // Ensure the file is an image and not too large
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file_type, $allowed_types)) {
            die("Error: Invalid file type. Only JPG, PNG, and GIF are allowed.");
        }

        if ($file_size > 5242880) { // 5MB limit
            die("Error: File size is too large. Max size is 5MB.");
        }

        $location = "../Uploads/";
        if (!is_dir($location)) {
            mkdir($location, 0777, true);  // Create the directory if it doesn't exist
        }

        $image_path = $location . uniqid() . '_' . $name; // Generate a unique filename to prevent overwriting

        // Move file to the Uploads directory
        if (!move_uploaded_file($temp, $image_path)) {
            die("Error: Failed to move the uploaded file.");
        }

        // Use a custom ID if there's a missing one, otherwise use AUTO_INCREMENT
        if ($missing_id) {
            // Insert with specific product_id
            $query = "INSERT INTO product_items (product_id, product_name, category, quantity, price, special_instructions, product_image) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                die("Error preparing the SQL statement: " . $conn->error);
            }
            // Bind the missing ID explicitly
            $stmt->bind_param("isssdss", $missing_id, $item_name, $item_type, $stock, $price, $special_instructions, $image_path);
        } else {
            // Standard insert without specifying product_id (use AUTO_INCREMENT)
            $query = "INSERT INTO product_items (product_name, category, quantity, price, special_instructions, product_image) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                die("Error preparing the SQL statement: " . $conn->error);
            }
            $stmt->bind_param("sssdss", $item_name, $item_type, $stock, $price, $special_instructions, $image_path);
        }

        // Execute the query
        if ($stmt->execute()) {
            echo "Product added successfully.";

            // Log the activity with 'Add Product' as action_type
            $action_details = "Added a new product: " . $item_name . " (Category: " . $item_type . ")";
            logActivity($conn, $user_id, 'Add Product', $action_details);  // Log the action

        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        die("Error: No image file uploaded or file upload error.");
    }
}

$conn->close();
?>
