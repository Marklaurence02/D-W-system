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
            background-color: #f8f9fa; /* Light gray background */
            font-family: Arial, sans-serif;
        }

        .header {
            background-color: #ff6700; /* Orange header */
            color: white;
            padding: 20px 40px;
            text-align: center; /* Center the header text */
        }

        .header h1 {
            margin: 0;
            font-weight: bold;
            font-size: 2rem;
        }

        .footer {
            background-color: #ff6700;
            color: white;
            padding: 15px 20px;
            text-align: center;
            font-size: 0.9rem;
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

    </style>
</head>
<body>

    <!-- Header -->
    <header class="header d-flex align-items-center">
        <h1>DINE&WATCH</h1>
    </header>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row align-items-center">
            <!-- Logo Section -->
            <div class="col-12 col-md-6 text-center">
                <img src="Images/ADMINS.png" alt="Dine&Watch Logo" class="logo-container img-fluid">
            </div>

            <!-- Sign-In Form Section -->
            <div class="col-12 col-md-6">
                <div class="signup-card mx-auto">
                    <h2>Sign In</h2>
                    <?php include 'assets/process_ad-log-in .php'; ?>
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


                        <button type="submit" class="btn-next">Next</button>

                        <div class="small-text">
                            <p>Forgot your password? <a href="forgot_password.php">Reset it here</a></p>
                        </div>
                    </form>
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
