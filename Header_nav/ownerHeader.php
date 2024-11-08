<?php
// Start the session if it's not already started
session_name("owner_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


include_once "./assets/config.php";
?>

<!-- nav -->
<nav class="navbar navbar-expand-lg navbar-light px-5" style="background-color: #3B3131;">
    <a class="navbar-brand ml-5" href="./index.php">
        <img src="./images/admin.png" width="80" height="80" alt="Dine&Watch">
    </a>

    <div class="welc">
        <?php
        $welcomeMessage = "Welcome, Guest";

        if (isset($_SESSION['user_id'])) {
            $role = $_SESSION['role'];
            $username = $_SESSION['username'] ?? 'User';

            $welcomeMessage = "Welcome, " . htmlspecialchars($username) . " (" . htmlspecialchars($role) . ")";
        }

        echo $welcomeMessage;
        ?>
    </div>

    <ul class="navbar-nav mr-auto mt-2 mt-lg-0"></ul>
</nav>

