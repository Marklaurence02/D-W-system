<?php
// updateProfile.php

include_once "config.php";  // Include your DB connection file

// Include the PHPMailer files
require '../otpphpmailer/PHPMailer-master/src/Exception.php';
require '../otpphpmailer/PHPMailer-master/src/PHPMailer.php';
require '../otpphpmailer/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
session_name("user_session");
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to perform this action.']);
    exit;
}

// Function to send profile update notification
function sendProfileUpdateEmail($email) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Use Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'dinewatchph@gmail.com'; // Your Gmail email
        $mail->Password = 'ywed icaf boco yrzx'; // Your generated Google App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('dinewatchph@gmail.com', 'Dine&Watch Support');
        $mail->addAddress($email); // Recipient email

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Profile Updated Successfully';
        $mail->Body    = "<p>Dear User,</p>
                          <p>Your profile has been successfully updated. If this wasn't you, please contact support immediately.</p>
                          <p>Thank you for using Dine&Watch!</p>
                          <p>Best Regards,<br>Dine&Watch Support Team</p>";

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
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
    $password = trim($_POST['password']); // Retrieve the password from the form

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($username)) {
        echo json_encode(['status' => 'error', 'message' => 'First name, last name, email, and username are required.']);
        exit;
    }

    // Check if email or username already exists
    $check_sql = "SELECT user_id FROM users WHERE (email = ? OR username = ?) AND user_id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('ssi', $email, $username, $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email or username is already in use.']);
        exit;
    }

    // Prepare SQL statement for updating user details
    $sql = "UPDATE users SET first_name = ?, middle_initial = ?, last_name = ?, email = ?, zip_code = ?, contact_number = ?, address = ?, username = ?, updated_at = NOW()";
    $params = [$first_name, $middle_initial, $last_name, $email, $zip_code, $contact_number, $address, $username];

    // Check if a new password is provided
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password_hash = ?";
        $params[] = $hashed_password;
    }

    $sql .= " WHERE user_id = ?";
    $params[] = $user_id;

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare error: ' . $conn->error]);
        exit;
    }

    // Bind parameters dynamically
    $stmt->bind_param(str_repeat('s', count($params) - 1) . 'i', ...$params);

    // Execute the statement
    if ($stmt->execute()) {
        // Log the profile update action in activity_logs table
        $action_type = 'Update Profile';
        $action_details = "User {$username} updated their profile.";
        $sql_log = "INSERT INTO activity_logs (action_by, action_type, action_details) VALUES (?, ?, ?)";
        $stmt_log = $conn->prepare($sql_log);
        $stmt_log->bind_param('iss', $user_id, $action_type, $action_details);
        $stmt_log->execute();

        // Send email notification
        if (sendProfileUpdateEmail($email)) {
            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully and notification email sent!']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully, but notification email could not be sent.']);
        }
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
