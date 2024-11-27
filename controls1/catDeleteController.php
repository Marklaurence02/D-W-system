<?php

    include_once "./assets/config.php";
    session_start();
    
    // Get the category ID from POST data
    $c_id = $_POST['record'];

    // Get the logged-in user ID from the session
    $user_id = $_SESSION['user_id']; // Ensure 'user_id' is correctly set in the session

    // Delete the category
    $query = "DELETE FROM category WHERE category_id = '$c_id'";
    $data = mysqli_query($conn, $query);

    if ($data) {
        echo "Category Item Deleted";

        // Log the action in the activity_logs table
        $action_type = 'Delete Category';
        $action_details = "Category with ID $c_id was deleted by user $user_id.";

        $log_query = "INSERT INTO activity_logs (action_by, action_type, action_details) 
                      VALUES ('$user_id', '$action_type', '$action_details')";
        
        $log_data = mysqli_query($conn, $log_query);

        if (!$log_data) {
            // Optional: Notify if logging fails
            echo "Failed to log activity.";
        }
    } else {
        echo "Not able to delete";
    }

?>
