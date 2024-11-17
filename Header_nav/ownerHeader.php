<?php
// Start the session if it's not already started
session_name("owner_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once "./assets/config.php";
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
    height: 125px;
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
