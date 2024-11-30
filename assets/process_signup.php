<?php
// Include the database connection configuration
include 'config.php';

// Initialize variables
$email = $password = $firstName = $middleInitial = $lastName = $suffix = $address = $phone = $zipCode = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $firstName = trim($_POST['firstName']);
    $middleInitial = trim($_POST['middleInitial']);
    $lastName = trim($_POST['lastName']);
    $suffix = trim($_POST['suffix']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $zipCode = trim($_POST['zipCode']);
    
    // Generate a unique username (optional based on your logic)
    $username = strtolower($firstName[0] . $lastName); 

    // Check if the generated username already exists and make it unique if needed
    $checkUsernameQuery = "SELECT COUNT(*) AS count FROM users WHERE username LIKE ?";
    $stmt = $conn->prepare($checkUsernameQuery);
    $usernamePattern = $username . '%';
    $stmt->bind_param("s", $usernamePattern);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];

    // If a matching username is found, append a number
    if ($count > 0) {
        $username .= ($count + 1); // e.g., if 'mas' exists, use 'mas1'
    }

    // Hash the password for security
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Check if the email already exists in the database
    $checkQuery = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If a match is found, the email already exists
        $error = "An account with this email already exists. Please try a different email.";
    } else {
        // No existing account, proceed with inserting the new user
        $sql = "INSERT INTO users (first_name, middle_initial, last_name, suffix, address, contact_number, email, zip_code, username, password_hash, role) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'General User')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssss", $firstName, $middleInitial, $lastName, $suffix, $address, $phone, $email, $zipCode, $username, $passwordHash);

        if ($stmt->execute()) {
            // Redirect with a success message
            header("Location: ../login.php?signup=success");
            exit; // Ensure no further code is executed after the redirect
        } else {
            $error = "Error executing query: " . $stmt->error;
        }
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
