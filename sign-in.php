<?php
// Move the include to the very top of the file, before any HTML
include 'assets/process_log-in.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dine&Watch</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .header {
            background-color: #ff6700;
            color: white;
            padding: 20px 40px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-weight: bold;
            font-size: 2rem;
        }

        .logo-container {
            max-width: 350px;
            margin: 0 auto;
        }

        .signup-card {
            background-color: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 450px;
        }

        .signup-card h2 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            position: relative;
            margin-bottom: 20px;
        }

        label {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 16px;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        input:focus + label,
        input:not(:placeholder-shown) + label {
            top: -10px;
            font-size: 12px;
            color: #ff6700;
        }

        .recaptcha {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-top: 15px;
        }

        .btn-next {
            background-color: #333;
            color: white;
            width: 100%;
            padding: 15px;
            font-size: 1.1rem;
            border-radius: 8px;
            border: none;
            margin-top: 20px;
        }

        .btn-next:hover {
            background-color: #ff6700;
            color: white;
        }

        .small-text {
            font-size: 1rem;
            text-align: center;
            margin-top: 20px;
            color: #333;
        }

        .small-text a {
            color: #ff6700;
            text-decoration: none;
            font-weight: bold;
        }

        .small-text a:hover {
            text-decoration: underline;
        }

        .footer {
            background-color: #ff6700;
            color: white;
            padding: 15px 20px;
            text-align: center;
            font-size: 0.9rem;
            margin-top: auto;
        }

        .form-control {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #ff6700;
            box-shadow: 0 0 0 0.2rem rgba(255, 103, 0, 0.25);
        }

        label {
            background-color: white;
            padding: 0 5px;
        }

        .small-text a {
            color: #ff6700 !important;
        }

        .recaptcha {
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .signup-card {
                padding: 25px;
                margin: 15px;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header d-flex align-items-center">
        <div class="logo ms-4">
            <img src="Images/dinewatchlogo.png" alt="Dine & Watch Logo" class="logo-img" style="max-height: 50px;">
        </div>
    </header>

    <!-- Main Content -->
    <div class="container my-5">
    <div class="row align-items-center">
        <!-- Logo Section -->
        <div class="col-12 col-md-6 text-center">
            <img src="Images/logo.png" alt="Dine&Watch Logo" class="logo-container img-fluid">
        </div>

        <!-- Sign-In Form Section -->
        <div class="col-12 col-md-6">
            <div class="signup-card mx-auto">
                <h2>Sign In</h2>
                <form method="POST">
                    <!-- Email Input -->
                    <div class="form-group position-relative">
                        <input type="email" class="form-control" name="email" id="email" placeholder=" " required>
                        <label for="email">Email</label>
                    </div>

                    <!-- Password Input -->
                    <div class="form-group position-relative">
                        <input type="password" class="form-control" name="password" id="password" placeholder=" " required>
                        <label for="password">Password</label>
                        <i class="bx bx-show position-absolute top-50 end-0 translate-middle-y pe-3" id="togglePassword" style="cursor: pointer;"></i>
                    </div>

                    <!-- Google reCAPTCHA -->
                    <div class="recaptcha align-item-center">
                        <div class="g-recaptcha" data-sitekey="6LcL4IcqAAAAAPB8-0U66IZDAWf0xYVhi8qtQjL9"></div>
                    </div>

                    <button type="submit" class="btn-next">Next</button>

                    <!-- Links Section -->
               

<!-- Links Section -->
<div class="small-text mt-3 d-flex justify-content-between">
    <a href="forgot_password.php">Forgot your password?</a>
    <a href="sign-up.php">Don't have an account?</a>
</div>

                </form>
            </div>
        </div>
    </div>
</div>

    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 Dine&Watch. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS and Boxicons -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <!-- Password Toggle -->
    <script>
        const togglePassword = document.querySelector("#togglePassword");
        const passwordField = document.querySelector("#password");

        togglePassword.addEventListener("click", function () {
            const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
            passwordField.setAttribute("type", type);
            this.classList.toggle("bx-show");
            this.classList.toggle("bx-hide");
        });
    </script>

</body>
</html>


