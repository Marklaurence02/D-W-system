<?php
$config = include 'assets/config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT f.feedback_id, f.feedback_text, f.rating, f.created_at, 
               u.first_name, u.last_name, u.suffix
        FROM feedbacks f
        INNER JOIN users u ON f.user_id = u.user_id
        ORDER BY f.created_at DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Query Error: " . $conn->error);
}

// Fetch feedback data
$feedbacks = [];
while ($row = $result->fetch_assoc()) {
    $feedbacks[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Dine&Watch</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/Lpage.css">
</head>

<body>

    <header class="custom-header">
        <div class="logo">
            <img src="Images/dinewatchlogo.png" alt="Dine & Watch Logo" class="logo-img">
        </div>
        <nav class="nav-menu">
            <a href="#hero-section" class="active">HOME</a>
            <a href="menu.php">MENU</a>
            <a href="#premium-pizza-section">PREMIUMS</a>
            <a href="#contact-section">CONTACT US</a>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero-section py-5" id="hero-section">
        <div class="container">
            <div class="col-md-8 mx-auto text-center">
                <h1 class="display-4">
                    WELCOME TO
                    <img src="Images/dinewatchlogo.png" alt="Dine & Watch Logo" class="inline-logo">
                </h1>
                <p class="lead mt-4">Elevate Your Taste! <br> Elevate Your Experience!</p>
                <div class="mt-4">
                    <a href="sign-in.php" class="btn btn-primary btn-lg">
                        Reserve Now!
                    </a>
                </div>
            </div>
        </div>
    </section>


    <!-- Premium Pizza Section -->
    <section class="premium-pizza-section py-5" id="premium-pizza-section">
        <div class="container premium-pizza-section-container">
            <h2 class="text-center mb-5">Antonina's Premium Pizza</h2>
            <div id="pizzaCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <!-- Slide 1 -->
                    <div class="carousel-item active">
                        <div class="card shadow">
                            <img src="Images/bcb.png" alt="Bacon Cheeseburger Pizza" class="card-img-top rounded">
                            <div class="card-body">
                                <h3 class="card-title text-white">Bacon Cheeseburger - The "BCB"</h3>
                                <p class="card-text">
                                    House marinara, mozzarella cheese, special cheese sauce, bell pepper, onions, ground beef, and bacon.
                                </p>
                                <p class="text-danger"><em>Comes with Spicy Honey and Garlic Mayo Sauce!</em></p>
                            </div>
                        </div>
                    </div>
                    <!-- Slide 2 -->
                    <div class="carousel-item">
                        <div class="card shadow">
                            <img src="Images/antninaschoice.png" alt="Antonina's Choice Pizza" class="card-img-top rounded">
                            <div class="card-body">
                                <h3 class="card-title text-white">Antonina's Choice</h3>
                                <p class="card-text">
                                    House marinara, mozzarella cheese, cheddar cheese, Hungarian sausage, ham, beef, pepperoni, bell pepper, onions, black olives, and mushroom.
                                </p>
                                <p class="text-danger"><em>Comes with Spicy Honey and Garlic Mayo Sauce!</em></p>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="carousel-control-prev hidden-button" type="button" data-bs-target="#pizzaCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next hidden-button" type="button" data-bs-target="#pizzaCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Feedback Section -->
    <section class="feedback-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">FEEDBACKS</h2>

            <!-- Carousel -->
            <div id="feedbackCarousel" class="carousel slide" data-bs-ride="carousel">
                <!-- Carousel Indicators -->
                <div class="carousel-indicators">
                    <?php
                    foreach ($feedbacks as $index => $feedback) {
                        $active = $index === 0 ? 'active' : '';
                        echo "<button type='button' data-bs-target='#feedbackCarousel' data-bs-slide-to='$index' class='$active' aria-label='Slide " . ($index + 1) . "'></button>";
                    }
                    ?>
                </div>

                <!-- Carousel Inner -->
                <div class="carousel-inner">
                    <?php
                    if (count($feedbacks) > 0) {
                        foreach ($feedbacks as $index => $feedback) {
                            $active = $index === 0 ? 'active' : '';
                            $rating = $feedback['rating'];
                            $stars = str_repeat('<i class="bi bi-star-fill text-warning"></i>', $rating) .
                                    str_repeat('<i class="bi bi-star text-warning"></i>', 5 - $rating);

                            // Handle first and last names with fallback for missing values
                            $firstName = htmlspecialchars($feedback['first_name'] ?? '');
                            $lastName = htmlspecialchars($feedback['last_name'] ?? '');
                            $fullName = trim("$firstName $lastName"); // Ensure no extra spaces

                            echo "<div class='carousel-item $active'>
                                    <div class='row justify-content-center'>
                                        <div class='col-md-6'>
                                            <div class='feedback-card shadow p-4'>
                                                <div class='d-flex align-items-center mb-3'>
                                                    <div class='profile-icon d-flex justify-content-center align-items-center'>
                                                        <i class='bi bi-person-circle'></i>
                                                    </div>
                                                    <div class='ms-3'>
                                                        <h4 class='mb-1'>$fullName</h4>
                                                    </div>
                                                </div>
                                                <p>" . htmlspecialchars($feedback['feedback_text']) . "</p>
                                                <div class='rating'>$stars</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>";
                        }
                    } else {
                        echo "<div class='carousel-item active'>
                                <div class='row justify-content-center'>
                                    <div class='col-md-6'>
                                        <div class='feedback-card shadow p-4'>
                                            <p>No feedback available.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>";
                    }
                    ?>
                </div>

                <!-- Next and Previous Buttons -->
                <button class="carousel-control-prev hidden-button" type="button" data-bs-target="#feedbackCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next hidden-button" type="button" data-bs-target="#feedbackCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section py-5" id="contact-section">
        <div class="container">
            <div class="row align-items-center">
                <!-- Contact Us Section -->
                <div class="col-md-6">
                    <h3 class="mb-4 color">Contact Us!</h3>
                    <ul class="contact-list list-unstyled">
                        <li class="d-flex align-items-center mb-3">
                            <a href="https://www.instagram.com/antoninaspinoybite/" class="text-decoration-none text-dark d-flex align-items-center" target="_blank">
                                <i class="bi bi-instagram contact-icon"></i>
                                <span class="ms-3">
                                    DINE<span style="color: red; white-space: nowrap;">&</span>WATCH
                                </span>
                            </a>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <a href="https://www.facebook.com/antoninaspizza" class="text-decoration-none text-dark d-flex align-items-center" target="_blank">
                                <i class="bi bi-facebook contact-icon"></i>
                                <span class="ms-3">
                                    DINE<span style="color: red; white-space: nowrap;">&</span>WATCH
                                </span>
                            </a>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <a href="mailto:dinewatchph@gmail.com" class="text-decoration-none text-dark d-flex align-items-center">
                                <i class="bi bi-envelope-fill contact-icon"></i>
                                <span class="ms-3">dinewatchph@gmail.com</span>
                            </a>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <a href="tel:09123456781" class="text-decoration-none text-dark d-flex align-items-center">
                                <i class="bi bi-telephone-fill contact-icon"></i>
                                <span class="ms-3">
                                    Tel. No.: 0912-345-6781<br>
                                    Hotline: 110-1111
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>

            <!-- Locate Us Section -->
                <div class="col-md-6">
                    <h3 class="mb-4">Locate Us!</h3>
                    <div class="map-container">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1162.575179420833!2d121.00601970799309!3d13.864868865308477!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd0d005b22b469%3A0x72b9eefa974b7abd!2sAntonina&#39;s%20Pizza%20%26%20Coffee!5e1!3m2!1sen!2sph!4v1732404591270!5m2!1sen!2sph"
                            width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="footer py-3">
        <div class="container">
            <p class="mb-0">2024 - Made by Sound System Group - BSU - TNEU - Lipa Campus</p>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>

</html>