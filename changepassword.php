<?php
session_start();

// Check if the OTP was successfully verified and the email exists in the session
if (!isset($_SESSION['otp_email'])) {
    // Redirect to the OTP verification page if the OTP email is not set
    header('Location: forgot_password.php');
    exit();
}

// Include the database connection file (mysqli)
require_once 'assets/config.php'; // Database connection file (update with your DB credentials)

// Handle form submission to change the password
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate new password
    if (empty($newPassword) || empty($confirmPassword)) {
        echo "<script>alert('Both password fields are required.');</script>";
    } elseif ($newPassword !== $confirmPassword) {
        // Check if the new password and confirm password match
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        // Hash the new password securely
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Get the user's email from the session
        $email = $_SESSION['otp_email'];

        // Update the password in the database using mysqli
        $query = "UPDATE users SET password_hash = ? WHERE email = ?";
        if ($stmt = mysqli_prepare($conn, $query)) {
            // Bind the parameters
            mysqli_stmt_bind_param($stmt, 'ss', $hashedPassword, $email);

            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                // Clear session data to prevent resubmission
                unset($_SESSION['otp_email']);
                unset($_SESSION['otp']); // Clear OTP as well
                unset($_SESSION['otp_sent']); // Clear OTP sent flag

                // Success message and redirect to login page
                echo "<script>alert('Password successfully changed!'); window.location.href = './../sign-in.php';</script>";
            } else {
                echo "<script>alert('Error updating password. Please try again.');</script>";
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        } else {
            echo "<script>alert('Error preparing statement.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa; /* Light gray background */
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .footer {
            background-color: #ff6700;
            color: white;
            padding: 15px 20px;
            text-align: center;
            font-size: 0.9rem;
            margin-top: auto;
        }

        .header {
            background-color: #ff6700; /* Orange header */
            color: white;
            padding: 20px 40px;
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

        .password-container {
            background-color: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 450px;
            margin: 0 auto;
        }

        .password-container h2 {
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

        .btn-submit {
            background-color: #333;
            color: white;
            width: 100%;
            padding: 15px;
            font-size: 1.1rem;
            border-radius: 8px;
            border: none;
            margin-top: 20px;
        }

        .btn-submit:hover {
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

        /* Fade out animation */
        .fade-out {
            animation: fadeOut 3s forwards;
        }

        @keyframes fadeOut {
            0% {
                opacity: 1;
            }
            100% {
                opacity: 0;
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header">
        <h1>DINE&WATCH</h1>
    </header>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row align-items-center">
            <!-- Logo Section -->
            <div class="col-12 col-md-6 text-center">
                <img src="./../Images/logo.png" alt="Dine&Watch Logo" class="logo-container img-fluid">
            </div>

            <!-- Password Change Form Section -->
            <div class="col-12 col-md-6">
                <div class="password-container">
                    <h2>Change Your Password</h2>

                    <!-- Error Notification -->
                    <?php if (isset($_SESSION['password_error'])): ?>
                        <div class="alert alert-danger fade-out" role="alert" id="error-message">
                            <?php echo $_SESSION['password_error']; unset($_SESSION['password_error']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <!-- New Password Input -->
                        <div class="form-group position-relative">
                            <input type="password" class="form-control" name="new_password" id="new_password" placeholder=" " required>
                            <label for="new_password">New Password</label>
                        </div>

                        <!-- Confirm Password Input -->
                        <div class="form-group position-relative">
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder=" " required>
                            <label for="confirm_password">Confirm New Password</label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-submit">Change Password</button>

                        <div class="small-text">
                            <p>Remembered your password? <a href="sign-in.php">Log in</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <p>&copy; 2024 Dine&Watch. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS and Boxicons -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <!-- Custom JS to hide alert after 5 seconds -->
    <script>
        window.onload = function() {
            const errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                setTimeout(function() {
                    errorMessage.classList.add('fade-out');
                }, 5000); // Hide after 5 seconds
            }
        };
    </script>

</body>
</html>
