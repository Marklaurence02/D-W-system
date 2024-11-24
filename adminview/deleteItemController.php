<?php
include_once "../assets/config.php";

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

// Check if the product ID is set
if (isset($_POST['record'])) {
    $p_id = intval($_POST['record']); // Ensure product ID is treated as an integer

    // Fetch the product name and image path before deleting the product
    $query_fetch = "SELECT product_name, product_image FROM product_items WHERE product_id = ?";
    if ($stmt_fetch = $conn->prepare($query_fetch)) {
        $stmt_fetch->bind_param("i", $p_id);
        $stmt_fetch->execute();
        $stmt_fetch->bind_result($product_name, $image_path);
        $stmt_fetch->fetch();
        $stmt_fetch->close();

        // Prepare and execute the deletion query regardless of whether the image exists or not
        $query = "DELETE FROM product_items WHERE product_id = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("i", $p_id);
            if ($stmt->execute()) {
                // If the product is deleted, also try to delete the image file if it exists and is not empty
                if (!empty($image_path) && file_exists($image_path)) {
                    unlink($image_path); // Delete the image file
                }

                echo "Product Item Deleted";

                // Log the activity with 'Delete Product' as action_type
                $action_details = "Deleted product: " . $product_name;
                logActivity($conn, $user_id, 'Delete Product', $action_details);  // Log the action

            } else {
                echo "Error: Unable to execute product deletion. SQL Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error: Failed to prepare the SQL DELETE statement. SQL Error: " . $conn->error;
        }
    } else {
        echo "Error: Failed to prepare the SQL SELECT statement. SQL Error: " . $conn->error;
    }
} else {
    echo "Error: Invalid product ID.";
}

$conn->close();
?>
