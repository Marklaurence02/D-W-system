<?php
include_once "assets/config.php";
?>

<!-- Sidebar -->
<div class="md-3 d-flex flex-column flex-shrink-0 p-3 text-white" style="background-color: rgba(253, 102, 16, 0.8); " id="mySidebar">
<div class="text-center mb-4" id="welcomeMessage">
    <!-- Logo displayed when the sidebar is collapsed -->
    <img src="./images/admin.png" class="logo d-md-none" width="50" height="50" alt="Logo" title="Dine&Watch">

    <!-- Profile picture and welcome message displayed when expanded -->
    <img src="./images/admin.png" class="rounded-circle profile-pic " width="80" height="80" alt="Dine&Watch">

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
    <ul class="nav nav-pills flex-column" style="align-content: space-around ">
    <li class="nav-item"><a href="Owner-panel.php"class="nav-link text-white"><i class="fa fa-cart-arrow-down"></i><span class="ml-2">Dashboard</span></a></li>
        <li class="nav-item"><a href="#orders" onclick="showOrders()" class="nav-link text-white"><i class="fa fa-cart-arrow-down sideicon"></i><span class="ml-2">Orders</span></a></li>
        <li class="nav-item"><a href="#reservation" onclick="showReservation()" class="nav-link text-white"><i class="fa fa-calendar-check-o sideicon"></i><span class="ml-2">Reservations</span></a></li>
        <li class="nav-item"><a href="#category" onclick="showCategory()" class="nav-link text-white"><i class="fa fa-line-chart sideicon"></i><span class="ml-2">Category</span></a></li>
        <li class="nav-item"><a href="#products" onclick="showProductItems()" class="nav-link text-white"><i class="fa fa-th-list sideicon"></i><span class="ml-2">Products</span></a></li>
        <li class="nav-item"><a href="#tables" onclick="showTableViews()" class="nav-link text-white"><i class="fa fa-th sideicon"></i><span class="ml-2">Tables</span></a></li>
        <li class="nav-item"><a href="#users" onclick="showUser()" class="nav-link text-white"><i class="fa fa-users sideicon"></i><span class="ml-2">Users</span></a></li>
        <li class="nav-item"><a href="#admin" onclick="showadmin()" class="nav-link text-white"><i class="fa fa-user-plus sideicon"></i><span class="ml-2">Admin Management</span></a></li>
        <li class="nav-item"><a href="#activity-log" onclick="showActivity_log()" class="nav-link text-white"><i class="fa fa-list-alt sideicon"></i><span class="ml-2">Activity Log</span></a></li>
        <li class="nav-item"><a href="message.php" class="nav-link text-white"><i class="fa fa-envelope sideicon"></i><span class="ml-2">Messages</span></a></li>
    </ul>
</div>



<style>

#mySidebar.collapsed .sideicon{
    font-size: 40px;
}


    #mySidebar {
    width: 260px;
    transition: all 0.4s ease;
}

#mySidebar.collapsed {
    width: 150px;
}

#mySidebar.collapsed .nav-link span {
    display: none;
}

#main {
    transition: margin-left 0.4s ease;
}

.openbtn {
    top: 10px;
    left: 20px;
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
/* Sidebar active state */
.nav-link.active {
    background-color: rgba(255, 255, 255, 0.2);
    border-left: 5px solid #FD6610;
    color: #fff;
    font-weight: bold;
}

/* Hover effect */
.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    transition: background-color 0.3s ease;
}

</style>