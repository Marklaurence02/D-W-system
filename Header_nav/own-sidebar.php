<?php
include_once "assets/config.php";
?>

<!-- Sidebar -->
<div class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white" id="mySidebar">
<div class="text-center mb-4" id="welcomeMessage">
    <!-- Logo displayed when the sidebar is collapsed -->
    <img src="./images/admin.png" class="logo d-md-none" width="50" height="50" alt="Logo" title="Dine&Watch">

    <!-- Profile picture and welcome message displayed when expanded -->
    <img src="./images/admin.png" class="rounded-circle profile-pic " width="80" height="80" alt="Dine&Watch">
    <button class="btn btn-dark openbtn" onclick="toggleNav()"><i class="fa fa-bars"></i></button>

    <h5 class="mt-3 d-none d-md-block" id="welcomeText">
        <?php
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $sql = "SELECT first_name FROM users WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user) {
                echo '<div>Welcome, <b>' . htmlspecialchars($user['first_name']) . '</b> (' . htmlspecialchars($_SESSION['role']) . ')</div>';
            } else {
                echo "<div>Welcome, User</div>";
            }
        } else {
            echo "<div>Welcome, Guest</div>";
        }
        ?>

    </h5>

    
</div>


    <hr class="bg-light">
    <a href="#" class="closebtn text-white d-md-none" onclick="toggleNav()">×</a>
    <ul class="nav nav-pills flex-column">
        <li class="nav-item"><a href="#orders" onclick="showOrders()" class="nav-link text-white"><i class="fa fa-cart-arrow-down"></i><span class="ml-2">Orders</span></a></li>
        <li class="nav-item"><a href="#reservation" onclick="showReservation()" class="nav-link text-white"><i class="fa fa-calendar-check-o"></i><span class="ml-2">Reservations</span></a></li>
        <li class="nav-item"><a href="#category" onclick="showCategory()" class="nav-link text-white"><i class="fa fa-line-chart"></i><span class="ml-2">Category</span></a></li>
        <li class="nav-item"><a href="#products" onclick="showProductItems()" class="nav-link text-white"><i class="fa fa-th-list"></i><span class="ml-2">Products</span></a></li>
        <li class="nav-item"><a href="#tables" onclick="showTableViews()" class="nav-link text-white"><i class="fa fa-th"></i><span class="ml-2">Tables</span></a></li>
        <li class="nav-item"><a href="#users" onclick="showUser()" class="nav-link text-white"><i class="fa fa-users"></i><span class="ml-2">Users</span></a></li>
        <li class="nav-item"><a href="#admin" onclick="showadmin()" class="nav-link text-white"><i class="fa fa-user-plus"></i><span class="ml-2">Admin Management</span></a></li>
        <li class="nav-item"><a href="#activity-log" onclick="showActivity_log()" class="nav-link text-white"><i class="fa fa-list-alt"></i><span class="ml-2">Activity Log</span></a></li>
        <li class="nav-item"><a href="message.php" class="nav-link text-white"><i class="fa fa-envelope"></i><span class="ml-2">Messages</span></a></li>
        <li class="nav-item"><a href="assets/ad-logout.php" class="nav-link text-white"><i class="fa fa-sign-out"></i><span class="ml-2">Log-out</span></a></li>
    </ul>
</div>



<style>
    #mySidebar {
    width: 250px;
    transition: all 0.4s ease;
}

#mySidebar.collapsed {
    width: 70px;
}

#mySidebar.collapsed .nav-link span {
    display: none;
}

#main {
    transition: margin-left 0.4s ease;
}

.openbtn {
    position: fixed;
    top: 10px;
    left: 20px;
    z-index: 1000;
}

/* Sidebar Collapsed State Styles */
#mySidebar.collapsed .profile-pic {
    display: none !important;
}

#mySidebar.collapsed #welcomeText {
    display: none !important;
}

#mySidebar .logo {
    display: none;
}

#mySidebar.collapsed .logo {
    display: block !important;
    margin: auto;
}

.profile-pic {
    transition: all 0.3s ease-in-out;
}/* Sidebar Collapsed State Styles */
#mySidebar.collapsed .profile-pic {
    display: none !important;
}

#mySidebar.collapsed #welcomeText {
    display: none !important;
}

#mySidebar .logo {
    display: none;
}

#mySidebar.collapsed .logo {
    display: block !important;
    margin: auto;
}

.profile-pic {
    transition: all 0.3s ease-in-out;
}
</style>