<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Dine&Watch</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel='stylesheet' type='text/css' media='screen' href='css/Lpage.css'>
</head>
<body>

    <!-- Header Section -->
    <?php include './Header_nav/Lpageheader.php'; ?>

  <!-- Hero Section -->
<section class="hero-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <!-- Left column with logo -->
            <div class="col-md-6 text-center text-md-start">
                <img src="Images/logo.png" alt="Dine&Watch Logo" class="img-fluid mb-4" style="max-width: 100%;">
            </div>

            <!-- Right column with introduction text and button -->
            <div class="col-md-6 text-center text-md-start">
                <h2 class="display-4">Welcome to Dine & Watch</h2>
                <p class="lead mt-4">
                    Experience the best dining while enjoying your favorite shows. Our carefully crafted menu and stunning ambiance offer you a perfect evening.
                </p>
                <p>
                    Whether you're dining with friends, family, or a special someone, we have something for everyone. Book a table now to secure your spot for an unforgettable experience!
                </p>
                <div class="mt-4">
                    <a href="index.php" class="btn btn-primary btn-lg">Reserve Now</a>
                </div>
            </div>
        </div>
    </div>
</section>



    <!-- Features Section -->
    <section class="features-section py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Featured Dishes and Events</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-item bg-white p-3">
                        <img src="Images/placeholder.png" alt="Feature Image" class="img-fluid">
                        <h3 class="mt-3">Featured Dish 1</h3>
                        <p>Description of the featured dish.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-item bg-white p-3">
                        <img src="Images/placeholder.png" alt="Feature Image" class="img-fluid">
                        <h3 class="mt-3">Featured Event</h3>
                        <p>Description of the event or show.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-item bg-white p-3">
                        <img src="Images/placeholder.png" alt="Feature Image" class="img-fluid">
                        <h3 class="mt-3">Featured Dish 2</h3>
                        <p>Description of the second featured dish.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feedback Section -->
    <section class="feedback-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">Customer Feedback</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feedback-item p-3 bg-light">
                        <p>"The food was excellent!"</p>
                        <p class="text-muted">- Customer A</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feedback-item p-3 bg-light">
                        <p>"Great atmosphere and entertainment."</p>
                        <p class="text-muted">- Customer B</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feedback-item p-3 bg-light">
                        <p>"I had a wonderful experience."</p>
                        <p class="text-muted">- Customer C</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Contact Us</h2>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li><strong>Address:</strong> 112</li>
                        <li><strong>Email:</strong> contact@dinewatch.com</li>
                        <li><strong>Phone:</strong> (123) 456-7890</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3873.557602531447!2d121.00298127234818!3d13.865573520679273!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd0d005b22b469%3A0x72b9eefa974b7abd!2sAntonina&#39;s%20Pizza%20%26%20Coffee!5e0!3m2!1sen!2sph!4v1730253771675!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </section> 

    <?php include './Header_nav/Lpagefooter.php'; ?>
    
    <!-- Bootstrap and Popper.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>