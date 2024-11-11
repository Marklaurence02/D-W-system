<?php
session_name("staff_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once "assets/config.php";
?>

<!-- nav -->
<nav class="navbar navbar-expand-lg navbar-light px-5" style="background-color: #3B3131;">
    <a class="navbar-brand ml-5" href="./index.php">
        <img src="./images/admin.png" width="80" height="80" alt="Dine&Watch">
    </a>

    <div class="welc">
        <?php
        // Default message for guest
        $welcomeMessage = "Welcome, Guest";

        // Check if the user is logged in
        if (isset($_SESSION['user_id'])) {
            // Use the session variable for role directly
            $role = $_SESSION['role'];
            $username = $_SESSION['username'] ?? 'User'; // Fallback in case username is not set

            // Set the welcome message based on user role
            $welcomeMessage = "Welcome, " . htmlspecialchars($username) . " (" . htmlspecialchars($role) . ")";
        }

        // Display the welcome message
        echo $welcomeMessage;
        ?>
    </div>

    <ul class="navbar-nav mr-auto mt-2 mt-lg-0"></ul>
    
    <div class="user-profile">
        <a href="<?php echo isset($_SESSION['user_id']) ? 'user_profile.php' : './assets/log-out.php'; ?>" style="text-decoration:none;">
            <i class="<?php echo isset($_SESSION['user_id']) ? 'fa fa-user' : 'fa fa-sign-in'; ?> mr-5 large-icon" style="font-size:30px; color:#fff;" aria-hidden="true"></i>
        </a>
    </div>
</nav>


