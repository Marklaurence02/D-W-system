<?php
// updateProfile.php

include_once "config.php";  // Include your DB connection file

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to perform this action.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = intval($_SESSION['user_id']);  // Use the session user ID
    $first_name = trim($_POST['first_name']);
    $middle_initial = trim($_POST['middle_initial']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $zip_code = trim($_POST['zip_code']);
    $contact_number = trim($_POST['contact_number']);
    $address = trim($_POST['address']);
    $username = trim($_POST['username']);

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($username)) {
        echo json_encode(['status' => 'error', 'message' => 'First name, last name, email, and username are required.']);
        exit;
    }

    // Prepare SQL statement for updating user details
    $sql = "UPDATE users SET first_name = ?, middle_initial = ?, last_name = ?, email = ?, zip_code = ?, contact_number = ?, address = ?, username = ?, updated_at = NOW() WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare error: ' . $conn->error]);
        exit;
    }

    // Bind parameters
    $stmt->bind_param("ssssssssi", $first_name, $middle_initial, $last_name, $email, $zip_code, $contact_number, $address, $username, $user_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating profile: ' . $stmt->error]);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
