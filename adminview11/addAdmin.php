<?php
// addAdmin.php

include_once "../assets/config.php"; // Ensure the path to the config file is correct

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if all necessary fields are set
    if (!empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['username']) 
        && !empty($_POST['password']) && !empty($_POST['role']) && !empty($_POST['contact_number']) 
        && !empty($_POST['email'])) {

        // Sanitize inputs
        $first_name = htmlspecialchars(trim($_POST['first_name']));
        $last_name = htmlspecialchars(trim($_POST['last_name']));
        $username = htmlspecialchars(trim($_POST['username']));
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
        $role = $_POST['role'];
        $contact_number = htmlspecialchars(trim($_POST['contact_number']));
        $email = htmlspecialchars(trim($_POST['email']));

        // Assume that the logged-in user's ID is stored in a session or similar method
        session_start(); // If you're using session for user login
        $user_id = $_SESSION['user_id']; // Make sure you set 'user_id' when the user logs in

        // Check if email or username already exists in the database
        $check_sql = "SELECT user_id, email, username FROM users WHERE email = ? OR username = ?";
        $check_stmt = $conn->prepare($check_sql);
        if ($check_stmt === false) {
            error_log("MySQL prepare error: " . $conn->error);
            echo json_encode(['status' => 'error', 'message' => 'Database preparation error while checking uniqueness.']);
            exit;
        }
        
        $check_stmt->bind_param("ss", $email, $username);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $check_stmt->bind_result($existing_user_id, $existing_email, $existing_username);
            $check_stmt->fetch();
            if ($existing_email === $email) {
                echo json_encode(['status' => 'error', 'message' => 'Email already exists. Please choose another.']);
            } elseif ($existing_username === $username) {
                echo json_encode(['status' => 'error', 'message' => 'Username already exists. Please choose another.']);
            }
            $check_stmt->close();
            exit;
        }
        $check_stmt->close();

        // Prepare SQL query to insert the new admin/staff member
        $sql = "INSERT INTO users (first_name, last_name, username, password_hash, role, contact_number, email, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            error_log("MySQL prepare error: " . $conn->error);
            echo json_encode(['status' => 'error', 'message' => 'Database preparation error.']);
            exit;
        }

        $stmt->bind_param("sssssss", $first_name, $last_name, $username, $password, $role, $contact_number, $email);

        if ($stmt->execute()) {
            // Log the activity for adding a new admin or staff member
            $action_type = "Add Admin"; // Correct action type
            $action_details = "$username added a new admin/staff member: $first_name $last_name";

            // Log the activity into the activity logs table
            $log_stmt = $conn->prepare("INSERT INTO activity_logs (action_by, action_type, action_details, created_at) VALUES (?, ?, ?, NOW())");
            if ($log_stmt === false) {
                error_log("MySQL prepare error: " . $conn->error);
            } else {
                $log_stmt->bind_param('iss', $user_id, $action_type, $action_details);
                $log_stmt->execute();
                $log_stmt->close();
            }

            echo json_encode(['status' => 'success', 'message' => 'Admin/Staff added successfully!']);
        } else {
            error_log("Execute error: " . $stmt->error);
            echo json_encode(['status' => 'error', 'message' => 'Error executing the query.']);
        }

        $stmt->close();
        $conn->close();

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Required fields are missing.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
