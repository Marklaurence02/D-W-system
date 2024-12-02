<?php
session_name("user_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set session timeout (e.g., 30 minutes)
$timeout_duration = 1800; // 30 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset(); // Unset session variables
    session_destroy(); // Destroy session
}
$_SESSION['last_activity'] = time(); // Update last activity time

include 'config.php';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim(htmlspecialchars($_POST['password']));

    // CAPTCHA validation
    $recaptcha_secret = '6LcL4IcqAAAAAIrgTS5nOBBsUwrzDbNKj4GjqQFD'; // Your secret key
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Verify CAPTCHA
    $recaptcha_verify_url = 'https://www.google.com/recaptcha/api/siteverify';
    $response = file_get_contents($recaptcha_verify_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $response_keys = json_decode($response, true);

    if ($response_keys['success'] !== true) {
        $_SESSION['error'] = "Please complete the CAPTCHA.";
    } else {
        if (!empty($email) && !empty($password)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Invalid email format.";
            } else {
                $sql = "SELECT user_id, first_name, last_name, username, password_hash, role 
                        FROM users WHERE email = ? AND role = 'General User'";
                $stmt = $conn->prepare($sql);

                if ($stmt) {
                    $stmt->bind_param('s', $email);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result && $result->num_rows > 0) {
                        $user = $result->fetch_assoc();

                        if (password_verify($password, $user['password_hash'])) {
                            $_SESSION['email'] = $email;
                            $_SESSION['role'] = 'General User';
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['user_id'] = $user['user_id'];

                            // Update the user's status to 'online'
                            $updateStatusSQL = "UPDATE users SET status = 'online' WHERE user_id = ?";
                            $updateStmt = $conn->prepare($updateStatusSQL);
                            $updateStmt->bind_param('i', $user['user_id']);
                            $updateStmt->execute();
                            $updateStmt->close();

                            // Assign a staff member to the user if not already assigned
                            if (!checkExistingAssignment($user['user_id'], $conn)) {
                                assignStaffToUser($user['user_id'], $conn);
                            }

                            // Redirect to the user panel
                            header('Location: User-panel.php');
                            exit();
                        } else {
                            $_SESSION['error'] = "Invalid password. Please try again.";
                        }
                    } else {
                        $_SESSION['error'] = "No user found with this email.";
                    }
                    $stmt->close();
                } else {
                    $_SESSION['error'] = "Database query error: " . $conn->error;
                }
            }
        } else {
            $_SESSION['error'] = "Please fill in all the required fields.";
        }
    }
}

$conn->close();

// Function to check if the user already has an assigned staff member
function checkExistingAssignment($userId, $conn) {
    $checkSQL = "SELECT assigned_staff_id FROM user_staff_assignments WHERE user_id = ?";
    $stmt = $conn->prepare($checkSQL);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->store_result();

    $isAssigned = $stmt->num_rows > 0;
    $stmt->close();

    return $isAssigned;
}

// Function to assign a staff member to a new general user
function assignStaffToUser($userId, $conn) {
    // Check if the user already has an assigned staff member
    $currentAssignmentSQL = "SELECT u.user_id, u.status 
                             FROM users u
                             JOIN user_staff_assignments usa ON u.user_id = usa.assigned_staff_id
                             WHERE usa.user_id = ?";
    $currentAssignmentStmt = $conn->prepare($currentAssignmentSQL);
    $currentAssignmentStmt->bind_param('i', $userId);
    $currentAssignmentStmt->execute();
    $currentAssignmentResult = $currentAssignmentStmt->get_result();

    if ($currentAssignmentResult && $currentAssignmentResult->num_rows > 0) {
        $currentAssignment = $currentAssignmentResult->fetch_assoc();
        if ($currentAssignment['status'] !== 'online') {
            // Delete the current assignment if the staff is offline
            $deleteAssignmentSQL = "DELETE FROM user_staff_assignments WHERE user_id = ?";
            $deleteAssignmentStmt = $conn->prepare($deleteAssignmentSQL);
            $deleteAssignmentStmt->bind_param('i', $userId);
            $deleteAssignmentStmt->execute();
            $deleteAssignmentStmt->close();
        } else {
            // If the current staff is online, no need to reassign
            $currentAssignmentStmt->close();
            return;
        }
    }
    $currentAssignmentStmt->close();

    // Get the list of all staff members and their assignment counts
    $staffSQL = "
        SELECT u.user_id, u.status, COUNT(usa.user_id) AS assigned_count 
        FROM users u
        LEFT JOIN user_staff_assignments usa ON u.user_id = usa.assigned_staff_id
        WHERE u.role = 'Staff'
        GROUP BY u.user_id
        ORDER BY assigned_count ASC, u.user_id ASC";
    
    $staffStmt = $conn->prepare($staffSQL);
    $staffStmt->execute();
    $staffResult = $staffStmt->get_result();
    
    if ($staffResult && $staffResult->num_rows > 0) {
        while ($staff = $staffResult->fetch_assoc()) {
            if ($staff['status'] === 'online') {
                $assignedStaffId = $staff['user_id'];

                // Assign the selected staff to the user
                $assignSQL = "INSERT INTO user_staff_assignments (user_id, assigned_staff_id) VALUES (?, ?)";
                $assignStmt = $conn->prepare($assignSQL);
                $assignStmt->bind_param('ii', $userId, $assignedStaffId);
                $assignStmt->execute();
                $assignStmt->close();
                break; // Exit the loop once an online staff is assigned
            }
        }
    }

    $staffStmt->close();
}
?>
