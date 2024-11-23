<?php
// Start the session with a custom session name if it's not already started

    session_start();


include_once "./assets/config.php"; // Include the DB connection file

// Function to check if the user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

// Function to initialize the session with user data
function initialize_session($user_id, $username, $role) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;
}

// Function to log the user out
function logout() {
    // Destroy the session data
    $_SESSION = [];
    session_unset();
    session_destroy();

    // Redirect to login page (adjust URL as needed)
    header("Location: ../ad-sign-in.php");
    exit();
}

// Handle user login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];

    // Check if username and password are provided
    if (!empty($username) && !empty($password)) {
        // Query to fetch user data from the database
        $login_sql = "SELECT user_id, username, password_hash, role FROM users WHERE username = ?";
        $stmt = $conn->prepare($login_sql);

        if ($stmt === false) {
            error_log("MySQL prepare error: " . $conn->error);
            echo json_encode(['status' => 'error', 'message' => 'Database error.']);
            exit;
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($user_id, $db_username, $db_password_hash, $role);

        if ($stmt->fetch() && password_verify($password, $db_password_hash)) {
            // Initialize session after successful login
            initialize_session($user_id, $db_username, $role);

            // Redirect user based on role (adjust URLs as needed)
            if ($role == 'Owner' || $role == 'Admin') {
                header("Location: ../admin_dashboard.php");
            } else {
                header("Location: ../user_dashboard.php");
            }
            exit();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Please provide both username and password.']);
    }
}

// Logout handling (if the user is logged in and requests to log out)
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    logout();
}

?>

<nav class="navbar navbar-expand-lg navbar-light bg-orange w-100">
    <div class="container-fluid d-flex justify-content-between">
        <!-- Sidebar Toggle Button -->
        <button class="btn openbtn" onclick="toggleNav()">
            <i class="fa fa-bars"></i>
        </button>

        <!-- Logout Button -->
        <a href="assets/ad-logout.php" class="text-white">
            <i class="fa fa-sign-out"></i> Log-out
        </a>
    </div>
</nav>

<style>
/* Navbar Styling */
.navbar {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 80px;
}

.bg-orange {
    background-color: rgba(253, 102, 16, 0.8);
}

.openbtn {
    margin-right: 15px;
}

a.text-white {
    color: white;
    text-decoration: none;
}

a.text-white:hover {
    color: #ffcc99;
}
</style>
