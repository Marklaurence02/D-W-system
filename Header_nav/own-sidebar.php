<?php
// Start the session at the top of the file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once "assets/config.php";
?>

<!-- Sidebar -->
<div class="sidebar" id="mySidebar">
    <div class="side-header">
        <img src="./images/admin.png" width="120" height="120" alt="Dine&Watch">
        <h5 style="margin-top:100px;">
            <?php
                // Check if the user is logged in
                if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];
                    $sql = "SELECT first_name FROM users WHERE user_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('i', $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();

                    if ($user) {
                        // Welcome message using your specified format
                        echo '<div class="welc">Welcome, ' . htmlspecialchars($user['first_name']) . ' (' . htmlspecialchars($_SESSION['role']) . ')</div>';
                    } else {
                        echo "Hello, User";
                    }
                } else {
                    echo "Hello, Guest";
                }
            ?>
        </h5>
    </div>
    
    <hr style="border:1px solid; background-color:#8a7b6d; border-color:#3B3131;">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
    <a href="owner-panel.php"><i class="fa fa-line-chart"></i> Dashboard</a>
    <a href="#orders" onclick="showOrders()"><i class="fa fa-cart-arrow-down"></i> Orders</a>
    <a href="#reservation" onclick="showReservation()"><i class="fa fa-calendar-check-o"></i> Reservations</a>
    <a href="#category" onclick="showCategory()"><i class="fa fa-line-chart"></i> Category</a>
    <a href="#products" onclick="showProductItems()"><i class="fa fa-th-list"></i> Products</a>
    <a href="#tables" onclick="showTableViews()"><i class="fa fa-th"></i> Tables</a>
    <a href="#users" onclick="showUser()"><i class="fa fa-users"></i> Users</a>
    <a href="#admin" onclick="showadmin()"><i class="fa fa-user-plus"></i></i> Admin Management</a>
    <a href="#activity-log" onclick="showActivity_log()"><i class="fa fa-list-alt"></i> Activity Log</a>
    <a href="message.php"><i class="fa fa-list-alt"></i> Messages</a>

    <a href="assets/ad-logout.php"><i class="fa fa-sign-out"></i> Log-out</a>
</div>

<div id="main">
    <button class="openbtn" onclick="openNav()"><i class="fa fa-bars"></i></button>
</div>
