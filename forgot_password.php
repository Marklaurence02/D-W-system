<?php
// Include the PHPMailer files
require './otpphpmailer/PHPMailer-master/src/Exception.php';
require './otpphpmailer/PHPMailer-master/src/PHPMailer.php';
require './otpphpmailer/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include your database connection
include_once 'assets/config.php';

if (!isset($conn)) {
    die("Database connection is not established. Please check config.php.");
}

session_start();

// Generate a 6-digit OTP
function generateOtp() {
    return rand(100000, 999999);
}

// Send OTP email
function sendOtpEmail($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Use Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'dinewatchph@gmail.com'; // Your Gmail email
        $mail->Password = 'ywed icaf boco yrzx'; // Your generated Google App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('dinewatchph@gmail.com', 'Dine&Watch Support');
        $mail->addAddress($email); // Recipient email

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body    = "Your OTP code is: <b>$otp</b>";

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}

// Handle OTP sending
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_otp'])) {
    $email = $_POST['email'];

    // Check if the email exists in the database using mysqli
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query); // Use $conn instead of $con
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Email exists, generate OTP and send email
        $otp = generateOtp();
        $_SESSION['otp'] = $otp; // Store OTP in session for verification
        $_SESSION['otp_email'] = $email; // Store email for tracking

        if (sendOtpEmail($email, $otp)) {
            $_SESSION['otp_sent'] = true; // Flag to show OTP form
            header('Location: verify-otp-mail.php'); // Redirect to OTP form
            exit();
        } else {
            echo "<script>alert('Failed to send OTP. Please try again.');</script>";
        }
    } else {
        // Email doesn't exist in the database
        echo "<script>alert('The email you entered is not registered. Please try again.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dine&Watch - Forgot Password</title>

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
        .footer {
            background-color: #ff6700;
            color: white;
            padding: 15px 20px;
            text-align: center;
            font-size: 0.9rem;
            margin-top: auto;
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
    <header class="header d-flex align-items-center justify-content-center">
        <h1>DINE&WATCH</h1>
    </header>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row align-items-center">
            <!-- Logo Section -->
            <div class="col-12 col-md-6 text-center">
                <img src="./../Images/logo.png" alt="Dine&Watch Logo" class="logo-container img-fluid">
            </div>
            <!-- OTP Form -->
            <div class="col-12 col-md-6">
                <div class="signup-card">
                    <h2>Enter Your Email</h2>
                    <form method="POST">
                        <div class="form-group  position-relative">
                            <input type="email" class="form-control" id="email" name="email" placeholder=" " required>
                            <label for="email">Email:</label>
                        </div>
                        <button type="submit" name="send_otp" class="btn-next">Send OTP</button>
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
</body>
</html>