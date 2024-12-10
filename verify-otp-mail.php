<?php
session_start();

// Include PHPMailer
require './otpphpmailer/PHPMailer-master/src/Exception.php';
require './otpphpmailer/PHPMailer-master/src/PHPMailer.php';
require './otpphpmailer/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the OTP has been sent and stored in the session
if (!isset($_SESSION['otp_sent'])) {
    header('Location: forgot_password.php'); // Redirect to the email input page if OTP wasn't sent
    exit();
}

// Handle OTP verification
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredOtp = $_POST['otp'];

    // Check if OTP and email are stored in the session
    if (isset($_SESSION['otp']) && isset($_SESSION['otp_email'])) {
        // Verify OTP
        if ($enteredOtp == $_SESSION['otp']) {
            // Successfully verified OTP
            // Redirect to change-password.php
            header('Location: changepassword.php');
            exit(); // Ensure no further code is executed after redirect
        } else {
            // Incorrect OTP
            $_SESSION['otp_error'] = 'Incorrect OTP. Please try again.';
        }
    } else {
        // Session expired or invalid state
        $_SESSION['otp_error'] = 'Session expired. Please request a new OTP.';
    }
}

// Handle OTP resend request
if (isset($_GET['resend'])) {
    // Ensure the email is set in the session
    if (isset($_SESSION['otp_email'])) {
        // Logic to generate and send a new OTP
        $_SESSION['otp'] = generateNewOtp(); // Generate a new OTP
        $_SESSION['otp_sent'] = true; // Set the OTP sent flag
        $_SESSION['resend_time'] = time(); // Set the resend time
        $_SESSION['resend_triggered'] = true; // Set the resend triggered flag
        if (sendOtpEmail($_SESSION['otp_email'], $_SESSION['otp'])) { // Use the new function
            $_SESSION['otp_error'] = 'A new OTP has been sent to your email.';
        } else {
            $_SESSION['otp_error'] = 'Failed to send OTP. Please try again later.';
        }
    } else {
        // Handle case where email is not set
        $_SESSION['otp_error'] = 'Email not set. Please request a new OTP.';
    }
}

function generateNewOtp() {
    // Generate a 6-digit random number
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

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
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="icon" type="image/png" href="images/icon1.png">


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
            min-height: 100vh; /* Ensures body takes at least the full height of the viewport */
        }

        .footer {
            background-color: #ff6700;
            color: white;
            padding: 15px 20px;
            text-align: center;
            font-size: 0.9rem;
            margin-top: auto; /* Pushes footer to the bottom */
            width: 100%; /* Ensures footer takes up full width */
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

        .otp-container {
            background-color: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 450px;
            margin: 0 auto;
            text-align: center;
        }

        .otp-input-group {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }

        .otp-input {
            width: 50px;
            height: 50px;
            border: 2px solid #ddd;
            border-radius: 8px;
            text-align: center;
            font-size: 20px;
            transition: all 0.3s ease;
            transform-origin: center;
        }

        .otp-input:focus {
            border-color: #ff6700;
            outline: none;
            box-shadow: 0 0 5px rgba(255, 103, 0, 0.3);
            transform: scale(1.05);
        }

        .otp-input.filled {
            background-color: #f8f9fa;
            border-color: #ff6700;
            animation: bounce 0.3s ease-in-out;
        }

        .otp-input.error {
            border-color: #dc3545;
            animation: shake 0.3s ease-in-out;
        }

        @keyframes bounce {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .verification-title {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }

        .verification-subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .resend-link {
            color: #ff6700;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .form-group label {
            display: none;
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

        .custom-alert {
            display: flex;
            align-items: center;
            background-color: #ffe5e5;
            border: 1px solid #ffcccc;
            border-radius: 8px;
            padding: 12px 15px;
            margin-top: 20px;
            color: #cc0000;
            font-size: 14px;
            position: relative;
            overflow: hidden;
        }

        .custom-alert::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            background-color: #cc0000;
        }

        .alert-icon {
            font-size: 20px;
            margin-right: 10px;
            color: #cc0000;
        }

        .alert-content {
            flex: 1;
        }

        /* Update the fade-out animation */
        .fade-out {
            animation: fadeOut 0.5s ease-in-out 2.5s forwards;
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-20px);
                display: none;
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
                <img src="./../Images/logo.png" alt="Dine&Watch Logo" class="logo-container img-fluid">
            </div>

            <!-- OTP Form Section -->
            <div class="col-12 col-md-6">
                <div class="otp-container">
                    <h2 class="verification-title">Email Verification</h2>
                    <p class="verification-subtitle">Enter the code we just sent to your email: <?php echo isset($_SESSION['otp_email']) ? $_SESSION['otp_email'] : ''; ?></p>

                    <form method="POST">
                        <div class="otp-input-group">
                            <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required oninput="moveToNext(this, 'otp2')">
                            <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required id="otp2" oninput="moveToNext(this, 'otp3')">
                            <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required id="otp3" oninput="moveToNext(this, 'otp4')">
                            <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required id="otp4" oninput="moveToNext(this, 'otp5')">
                            <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required id="otp5" oninput="moveToNext(this, 'otp6')">
                            <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required id="otp6" oninput="moveToNext(this, null)">
                        </div>
                        
                        <!-- Hidden input to store complete OTP -->
                        <input type="hidden" name="otp" id="complete-otp">

                        <div class="small-text">
                            <p>Don't receive the code? 
                                <a href="verify-otp-mail.php?resend=true" class="resend-link" id="resend-link">Resend</a>
                            </p>
                            <p id="countdown-timer" class="text-muted"></p>
                            <p id="timer" style="color: #666; font-size: 14px;"></p>
                        </div>
                        
                        <!-- Error Notification -->
                        <?php if (isset($_SESSION['otp_error'])): ?>
                            <div class="alert custom-alert fade-out" role="alert" id="error-message">
                                <i class='bx bx-error-circle alert-icon'></i>
                                <div class="alert-content">
                                    <?php echo $_SESSION['otp_error']; unset($_SESSION['otp_error']); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <button type="submit" class="btn-submit">Verify OTP</button>
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

    <!-- Custom JS to hide alert after 5 seconds and countdown timer -->
    <script>
        window.onload = function() {
            const errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                setTimeout(function() {
                    errorMessage.classList.add('fade-out');
                }, 5000); // Hide after 5 seconds
            }

            const resendLink = document.getElementById('resend-link');
            const countdownTimer = document.getElementById('countdown-timer');
            let countdown = 60; // 30 seconds countdown

            // Check if the resend was triggered
            <?php if (isset($_SESSION['resend_triggered'])): ?>
                resendLink.style.pointerEvents = 'none'; // Disable link
                countdownTimer.innerText = `You can resend OTP in ${countdown} seconds.`;

                const interval = setInterval(() => {
                    countdown--;
                    if (countdown > 0) {
                        countdownTimer.innerText = `You can resend OTP in ${countdown} seconds.`;
                    } else {
                        clearInterval(interval);
                        resendLink.style.pointerEvents = 'auto'; // Enable link
                        countdownTimer.innerText = ''; // Clear the countdown text
                    }
                }, 1000);

                <?php unset($_SESSION['resend_triggered']); // Clear the flag ?>
            <?php endif; ?>
        };
    </script>

    <script>
        function moveToNext(current, nextFieldID) {
            if (current.value.length >= 1) {
                if (nextFieldID) {
                    document.getElementById(nextFieldID).focus();
                }
                updateCompleteOtp();
            }
        }

        function updateCompleteOtp() {
            const otpInputs = document.querySelectorAll('.otp-input');
            let completeOtp = '';
            otpInputs.forEach(input => {
                completeOtp += input.value;
            });
            document.getElementById('complete-otp').value = completeOtp;
        }
    </script>

</body>
</html>
