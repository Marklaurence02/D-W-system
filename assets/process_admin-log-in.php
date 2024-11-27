<?php

    session_start();

// Set session timeout
$timeout_duration = 1800; // 30 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
}
$_SESSION['last_activity'] = time();

// Include the database connection
include 'config.php';

// Initialize variables
$message = '';
$error = '';

// Function to handle errors
function handleError($errorMessage) {
    $_SESSION['error'] = $errorMessage;
    header('Location: ad-sign-in.php');
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and capture form inputs
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim(htmlspecialchars($_POST['password']));

    // Basic validation
    if (!empty($email) && !empty($password)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            handleError("Invalid email format.");
        } else {
            // Prepare the SQL statement to fetch user data
            $sql = "SELECT password_hash, role, username, user_id FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);

            // Check for errors in preparing the statement
            if (!$stmt) {
                handleError("Database query error: " . $conn->error);
            }

            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                // User found, now check the password
                $row = $result->fetch_assoc();
                $hashedPassword = $row['password_hash'];
                $role = $row['role'];
                $username = $row['username'];
                $user_id = $row['user_id']; // Capture user_id

                // Verify the entered password against the hashed password
                if (password_verify($password, $hashedPassword)) {
                    // Set session variables
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $role;
                    $_SESSION['email'] = $email;

                    // Update user's status to 'online' after successful login
                    $update_status_sql = "UPDATE users SET status = 'online' WHERE user_id = ?";
                    $update_stmt = $conn->prepare($update_status_sql);
                    $update_stmt->bind_param('i', $user_id);
                    $update_stmt->execute();

                    // Generate a session token
                    $session_token = bin2hex(random_bytes(32)); // Generate a secure session token

                    // Store session in the sessions table
                    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // 1 hour session expiry
                    $sql = "INSERT INTO sessions (user_id, session_token, created_at, expires_at) VALUES (?, ?, NOW(), ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('iss', $user_id, $session_token, $expires_at);
                    $stmt->execute();
                    $_SESSION['session_token'] = $session_token;

                    // Log the login action in activity_logs table
                    $action_type = 'Login';
                    $action_details = "User {$username} Login ";
                    $sql = "INSERT INTO activity_logs (action_by, action_type, action_details) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('iss', $user_id, $action_type, $action_details);
                    $stmt->execute();

                    // Redirect based on role
                    switch ($role) {
                        case 'Owner':
                            header('Location: Owner-panel.php');
                            break;
                        case 'Admin':
                            header('Location: Admin-panel.php');
                            break;
                        case 'Staff':
                            header('Location: Staff-panel.php');
                            break;
                        default:
                            handleError("Unknown role. Please contact support.");
                    }
                } else {
                    handleError("Invalid password. Please try again.");
                }
            } else {
                handleError("No user found with this email.");
            }
        }
    } else {
        handleError("Please fill in all the required fields.");
    }
}

// Close the database connection
$conn->close();
