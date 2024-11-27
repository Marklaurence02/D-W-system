<?php
include_once "../assets/config.php";  // Include database connection

session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Function to log the user's actions
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

if (isset($_POST['table_id'])) {
    $table_id = intval($_POST['table_id']);
    
    // Start transaction
    $conn->begin_transaction();

    // First, fetch table details to log the action and handle images
    $query_fetch = "SELECT table_number FROM tables WHERE table_id = ?";
    if ($stmt_fetch = $conn->prepare($query_fetch)) {
        $stmt_fetch->bind_param("i", $table_id);
        $stmt_fetch->execute();
        $stmt_fetch->bind_result($table_number);
        $stmt_fetch->fetch();
        $stmt_fetch->close();

        // Fetch related images to delete them as well
        $query_fetch_images = "SELECT image_path FROM table_images WHERE table_id = ?";
        if ($stmt_fetch_images = $conn->prepare($query_fetch_images)) {
            $stmt_fetch_images->bind_param("i", $table_id);
            $stmt_fetch_images->execute();
            $result_images = $stmt_fetch_images->get_result();
            $image_paths = [];
            while ($row = $result_images->fetch_assoc()) {
                $image_paths[] = $row['image_path'];
            }
            $stmt_fetch_images->close();

            // Delete table images from the filesystem
            foreach ($image_paths as $image_path) {
                if (!empty($image_path) && file_exists($image_path)) {
                    unlink($image_path);  // Delete the image file from the server
                }
            }

            // Delete images from the table_images table
            $query_delete_images = "DELETE FROM table_images WHERE table_id = ?";
            if ($stmt_delete_images = $conn->prepare($query_delete_images)) {
                $stmt_delete_images->bind_param("i", $table_id);
                $stmt_delete_images->execute();
                $stmt_delete_images->close();
            } else {
                // Rollback on error
                $conn->rollback();
                echo "Error: Failed to delete table images. SQL Error: " . $conn->error;
                exit;
            }

            // Delete the table from the tables table
            $query_delete_table = "DELETE FROM tables WHERE table_id = ?";
            if ($stmt_delete_table = $conn->prepare($query_delete_table)) {
                $stmt_delete_table->bind_param("i", $table_id);
                if ($stmt_delete_table->execute()) {
                    echo "Table Deleted";

                    // Log the activity after deletion
                    $action_details = "Deleted table number: " . $table_number;
                    logActivity($conn, $user_id, 'Delete Table', $action_details);

                    // Commit the transaction after successful execution
                    $conn->commit();
                } else {
                    $conn->rollback();  // Rollback if deletion fails
                    echo "Error: Unable to delete table. SQL Error: " . $stmt_delete_table->error;
                }
                $stmt_delete_table->close();
            } else {
                $conn->rollback();
                echo "Error: Failed to prepare the SQL DELETE statement for the table. SQL Error: " . $conn->error;
            }

        } else {
            echo "Error: Failed to prepare the SQL SELECT statement for fetching images. SQL Error: " . $conn->error;
        }
    } else {
        echo "Error: Failed to fetch table details. SQL Error: " . $conn->error;
    }
} else {
    echo "Error: Invalid table ID.";
}

$conn->close();
?>
