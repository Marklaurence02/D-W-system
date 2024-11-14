<?php
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
        // Prepare the SQL statement
        $sql = "SELECT password_hash, role, username, user_id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        // Check for errors in preparing the statement
        if (!$stmt) {
            die("Database query error: " . $conn->error);
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
                
                // Set session name based on role
                switch ($role) {
                    case 'Owner':
                        session_name("owner_session");
                        break;
                    case 'Admin':
                        session_name("admin_session");
                        break;
                    case 'Staff':
                        session_name("staff_session");
                        break;
                    default:
                        $error = "Unknown role. Please contact support.";
                        break;
                }

                // Start the session if not already started
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                // Update the user's status to 'online'
                $status_sql = "UPDATE users SET status = 'online' WHERE user_id = ?";
                $status_stmt = $conn->prepare($status_sql);
                $status_stmt->bind_param('i', $user_id);
                $status_stmt->execute();
                $status_stmt->close();

                // Store user info in session
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;
                $_SESSION['username'] = $username; // Store username for welcome message
                $_SESSION['user_id'] = $user_id;

                // Log the login activity
                $action_type = "Login";
                $action_details = "$username logged in";
                $log_sql = "INSERT INTO activity_logs (action_by, action_type, action_details) VALUES (?, ?, ?)";
                $log_stmt = $conn->prepare($log_sql);
                $log_stmt->bind_param('iss', $user_id, $action_type, $action_details);
                $log_stmt->execute();
                $log_stmt->close();

                // Redirect based on role and ensure exit to prevent further script execution
                switch ($role) {
                    case 'Owner':
                        header('Location: Owner-panel.php');
                        exit();
                    case 'Admin':
                        header('Location: Admin-panel.php');
                        exit();
                    case 'Staff':
                        header('Location: Staff-panel.php');
                        exit();
                    default:
                        $error = "Unknown role. Please contact support.";
                }
            } else {
                $error = "Invalid password. Please try again.";
            }
        } else {
            $error = "No user found with this email.";
        }
      
    } else {
        $error = "Please fill in all the required fields.";
    }
}

// Close the database connection
$conn->close();

// Display error message on the login page if it exists
if (!empty($error)) {
    // Optionally, you can redirect with an error message stored in the session
    $_SESSION['login_error'] = $error;
    header("Location: ad-sign-in.php");
    exit();
}
?>
