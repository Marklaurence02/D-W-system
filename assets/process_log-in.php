<?php
// Start the session with a custom session name
session_name("user_session");

// Check if a session has not already been started, and start one if necessary
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear the session array (removes any existing session data)
// NOTE: This line may reset your session data, which could cause issues with user sessions
$_SESSION = [];

// Include the database configuration file to establish a connection
include 'config.php';

// Initialize variables for storing error messages and responses
$message = '';
$error = '';

// Check if the form is submitted using the POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user inputs to prevent XSS (Cross-Site Scripting)
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim(htmlspecialchars($_POST['password']));

    // Check if both email and password fields are filled
    if (!empty($email) && !empty($password)) {
        // Validate if the provided email is in a correct format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Set error message if the email format is invalid
            $error = "Invalid email format.";
        } else {
            // Prepare an SQL statement to fetch user data with the specified email and role
            $sql = "SELECT user_id, first_name, middle_initial, last_name, suffix, username, password_hash, role 
                    FROM users WHERE email = ? AND role = 'General User'";
            $stmt = $conn->prepare($sql);

            // Check if the SQL statement preparation was successful
            if ($stmt) {
                // Bind the email parameter to the SQL query
                $stmt->bind_param('s', $email);
                $stmt->execute(); // Execute the query
                $result = $stmt->get_result(); // Get the result set

                // Check if a matching user was found in the database
                if ($result && $result->num_rows > 0) {
                    // Fetch the user data as an associative array
                    $row = $result->fetch_assoc();

                    // Verify the entered password against the hashed password stored in the database
                    if (password_verify($password, $row['password_hash'])) {
                        // Set session variables to store user information
                        $_SESSION['email'] = $email;
                        $_SESSION['role'] = 'General User';
                        $_SESSION['username'] = $row['username'];
                        $_SESSION['user_id'] = $row['user_id'];

                        // Update the user's status to 'online' in the database
                        $updateStatusSQL = "UPDATE users SET status = 'online' WHERE user_id = ?";
                        $updateStmt = $conn->prepare($updateStatusSQL);
                        if ($updateStmt) {
                            $updateStmt->bind_param('i', $row['user_id']);
                            $updateStmt->execute();
                            $updateStmt->close(); // Close the update statement
                        } else {
                            // Set error message if updating the status failed
                            $error = "Failed to update status: " . $conn->error;
                        }

                        // Redirect the user to the User-panel page to avoid form resubmission
                        header('Location: User-panel.php');
                        exit(); // Ensure no further code is executed after redirection
                    } else {
                        // Set error message if the password does not match
                        $error = "Invalid password. Please try again.";
                    }
                } else {
                    // Set error message if no user is found with the provided email
                    $error = "No user found with this email.";
                }
                $stmt->close(); // Close the statement
            } else {
                // Set error message if there was an issue with the database query
                $error = "Database query error: " . $conn->error;
            }
        }
    } else {
        // Set error message if either email or password field is empty
        $error = "Please fill in all the required fields.";
    }
}

// Close the database connection
$conn->close();

// Display an error message on the login page if one exists
if (!empty($error)) {
    echo "<p style='color: red;'>$error</p>";
}
?>
