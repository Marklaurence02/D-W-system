<?php
// addAdmin.php

include_once "../assets/config.php"; // Ensure the path to the config file is correct

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Debugging line: uncomment to check received POST data
    // var_dump($_POST);

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
