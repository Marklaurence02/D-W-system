<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Get the user_id and username from the session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/user.css">
    <link rel="stylesheet" href="css/Lpage.css">
</head>
<body>
    <?php include "Header_nav/Userheader.php"; ?>

    <!-- Main Content Section -->
    <div id="main-content" class="container allContent-section py-4 center-content">
        <h1 class="text-center">Welcome, <?= htmlspecialchars($username); ?>!</h1>
        <p class="text-center">Your User ID: <?= htmlspecialchars($user_id); ?></p>
        <h1 class="text-center">Delicious starts here!</h1>

        <!-- Table Navigation -->
        <div class="table_nav">
            <button class="table-nav">Reservation</button>
            <button class="table-nav">No of Guests</button>
            <button class="table-nav">Date</button>
            <button class="table-nav">Time</button>
        </div>

        <!-- Slider Section -->
        <div class="slider-container">
            <button class="arrow-btn" id="prev-btn"><i class="fa fa-arrow-circle-left arrow" aria-hidden="true"></i></button>
            <div class="slider">
                <div class="slide-track">
                    <!-- Empty Slide for spacing -->
                    <div class="slide-content-box empty-slide"></div>

                    <!-- Menu Slide -->
                    <div class="slide-content-box active-slide">
                        <div class="slide-button">
                            <div class="title">Menu</div>
                            <button class="select" onclick="ordertable()">
                                <span class="image-box">
                                    <i class="fa fa-cutlery big" aria-hidden="true"></i>
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Table Slide -->
                    <div class="slide-content-box">
                        <div class="slide-button">
                            <div class="title">Table</div>
                            <button class="select"> 
                                <div class="image-box">
                                    <i class="fa fa-calendar-check-o big" aria-hidden="true"></i>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Payment Slide -->
                    <div class="slide-content-box">
                        <div class="slide-button">
                            <div class="title">Payment</div>
                            <button class="select">
                                <div class="image-box">
                                    <i class="fa fa-credit-card big" aria-hidden="true"></i>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Empty Slide for spacing -->
                    <div class="slide-content-box empty-slide"></div>
                </div>
            </div>
            <button class="arrow-btn" id="next-btn"><i class="fa fa-arrow-circle-right arrow" aria-hidden="true"></i></button>
        </div>

        <!-- Navigation Dots -->
        <div class="slider-dots">
            <span class="dot" data-slide="0"></span>
            <span class="dot active" data-slide="1"></span>
            <span class="dot" data-slide="2"></span>
        </div>
    </div>

    <?php include "Header_nav/Userfooter.php"; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
    <script src="Js/slider.js"></script>
    <script src="Js/Userpanel.js"></script>
</body>
</html>
