<?php
// Start the session if it's not already started
session_name("owner_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once "./assets/config.php";
?>

<nav class="navbar navbar-expand-lg navbar-light bg-orange w-100">
    <div class="container-fluid">
        <button class="btn openbtn" onclick="toggleNav()"><i class="fa fa-bars"></i></button>


        <a href="assets/ad-logout.php" class="ml-auto text-white">
            <i class="fa fa-sign-out"></i> Log-out
        </a>
    </div>
</nav>

<!-- Optional Row Container -->
<div class="row" style="flex-wrap: nowrap;">
<Style>
 
</Style>