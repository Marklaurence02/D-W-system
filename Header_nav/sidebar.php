<?php
session_name("admin_session");
session_start();

include_once "assets/config.php";
?>

<div id="mySidebar" class="d-flex flex-column flex-shrink-0 p-3 text-white" style="background-color: rgba(253, 102, 16, 0.8);">
    <div class="text-center mb-4" id="welcomeMessage">
        <!-- Logo for Collapsed Sidebar -->
        <img src="./images/admin.png" class="logo d-md-none" width="50" height="50" alt="Logo" title="Dine&Watch">
        
        <!-- Profile Picture and Welcome Text -->
        <img src="./images/admin.png" class="rounded-circle profile-pic" width="80" height="80" alt="Dine&Watch">
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
    <ul class="nav nav-pills flex-column">
        <li class="nav-item">
            <a href="Owner-panel.php" class="nav-link text-white"><i class="fa fa-cart-arrow-down"></i> Dashboard</a>
            <a href="Admin-panel.php"><i class="fa fa-line-chart"></i> Dashboard</a>
    <a href="#orders" onclick="showOrders()"><i class="fa fa-cart-arrow-down"></i> Orders</a>
    <a href="#reservation" onclick="showReservation()"><i class="fa fa-calendar-check-o"></i> Reservations</a>
    <a href="#category" onclick="showCategory()"><i class="fa fa-line-chart"></i> Category</a>
    <a href="#products" onclick="showProductItems()"><i class="fa fa-th-list"></i> Products</a>
    <a href="#tables" onclick="showTableViews()"><i class="fa fa-th"></i> Tables</a>
    <a href="#users" onclick="showUser()"><i class="fa fa-users"></i> Users</a>
    <a href="assets/ad-logout.php"><i class="fa fa-sign-out"></i> Log-out</a>
        </li>
        <!-- Additional Sidebar Links -->
    </ul>
</div>

<style>
/* Sidebar Styling */
#mySidebar {
    width: 260px;
    transition: all 0.4s ease;
}

#mySidebar.collapsed {
    width: 80px;
}

.nav-link {
    padding: 10px 15px;
    transition: background-color 0.3s ease;
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.nav-link.active {
    background-color: rgba(255, 255, 255, 0.2);
    font-weight: bold;
}

.profile-pic {
    margin-bottom: 15px;
    transition: transform 0.3s ease;
}
</style>










<hr style="border:1px solid; background-color:#8a7b6d; border-color:#3B3131;">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
   