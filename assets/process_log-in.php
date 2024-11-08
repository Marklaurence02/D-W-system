<?php
// Set a unique session name for users to avoid session conflicts with admins
session_name("user_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear any old session data to avoid session conflicts
$_SESSION = [];

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
        $sql = "SELECT user_id, first_name, middle_initial, last_name, suffix, username, password_hash, role 
                FROM users WHERE email = ? AND role = 'General User'";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Database query error: " . $conn->error);
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password_hash'];
            $user_id = $row['user_id'];
            $username = $row['username'];

            // Verify the entered password against the hashed password
            if (password_verify($password, $hashedPassword)) {
                // Store user info in session
                $_SESSION['email'] = $email;
                $_SESSION['role'] = 'General User';
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $user_id;

                // Redirect to User Panel
                header('Location: User-panel.php');
                exit();
            } else {
                $error = "Invalid password. Please try again.";
            }
        } else {
            $error = "No user found with this email.";
        }
        $stmt->close();
    } else {
        $error = "Please fill in all the required fields.";
    }
}

$conn->close();
?>
