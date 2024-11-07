<?php
// Start the session
session_start();

// Include the database connection configuration
include 'config.php';

// Initialize message and error variables
$message = '';
$error = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and capture form inputs
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim(htmlspecialchars($_POST['password']));

    // Basic validation
    if (!empty($email) && !empty($password)) {
        // Prepare the SQL statement for General Users only
        $sql = "SELECT user_id, first_name, middle_initial, last_name, suffix, username, password_hash, role FROM users WHERE email = ? AND role = 'General User'";
        $stmt = $conn->prepare($sql);

        // Check for errors in preparing the statement
        if (!$stmt) {
            die("Database query error: " . $conn->error);
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User found, now check the password
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password_hash'];
            $user_id = $row['user_id'];
            $role = $row['role'];
            $username = $row['username']; // Fallback if full name is not constructed

            // Construct the full name if available
            if (!empty($row['first_name']) && !empty($row['last_name'])) {
                $username = $row['first_name'];
                if (!empty($row['middle_initial'])) {
                    $username .= ' ' . $row['middle_initial'] . '.';
                }
                $username .= ' ' . $row['last_name'];
                if (!empty($row['suffix'])) {
                    $username .= ' ' . $row['suffix'];
                }
            }

            // Verify the entered password against the hashed password
            if (password_verify($password, $hashedPassword)) {
                // Store user info in session
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;
                $_SESSION['username'] = $username; // Store full name if available
                $_SESSION['user_id'] = $user_id; // Store user_id

                // Log the login activity
                $action_type = "Login";
                $action_details = "$username logged in";
                $log_sql = "INSERT INTO activity_logs (action_by, action_type, action_details) VALUES (?, ?, ?)";
                $log_stmt = $conn->prepare($log_sql);
                $log_stmt->bind_param('iss', $user_id, $action_type, $action_details);
                $log_stmt->execute();
                $log_stmt->close(); // Close the statement

                // Redirect to General User dashboard
                header('Location: User-Panel.php');
                exit();
            } else {
                $error = "Invalid password. Please try again.";
            }
        } else {
            $error = "No user found with this email.";
        }
        $stmt->close(); // Close the prepared statement
    } else {
        $error = "Please fill in all the required fields.";
    }
}

// Close the database connection
$conn->close();
