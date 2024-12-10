<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['otp_email'])) {
    header('Location: forgot_password.php');
    exit();
}

require_once 'assets/config.php';

$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($newPassword) || empty($confirmPassword)) {
        $message = 'Both password fields are required.';
        $messageType = 'error';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'Passwords do not match.';
        $messageType = 'error';
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $email = $_SESSION['otp_email'];

        $query = "UPDATE users SET password_hash = ? WHERE email = ?";
        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, 'ss', $hashedPassword, $email);
            if (mysqli_stmt_execute($stmt)) {
                unset($_SESSION['otp_email'], $_SESSION['otp'], $_SESSION['otp_sent']);
                $message = 'Password successfully changed!';
                $messageType = 'success';
            } else {
                $message = 'Error updating password. Please try again.';
                $messageType = 'error';
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = 'Error preparing statement.';
            $messageType = 'error';
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
    <link rel="icon" type="image/png" href="images/icon1.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
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
            background-color: #ff6700;
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
        .password-container img {
            z-index: 1050; /* Ensure the image stays above the modal */
            position: relative; /* Add position relative to apply z-index */
        }
    </style>
</head>
<body>
    <header class="header d-flex align-items-center">
        <div class="logo ms-4">
            <img src="Images/dinewatchlogo.png" alt="Dine & Watch Logo" class="logo-img" style="max-height: 50px;">
        </div>
    </header>
    <div class="container my-5">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 text-center">
                <img src="./../Images/logo.png" alt="Dine&Watch Logo" class="logo-container img-fluid">
            </div>
            <div class="col-12 col-md-6">
                <div class="password-container">
                    <h2>Change Your Password</h2>
                    <form method="POST">
                        <div class="form-group position-relative">
                            <input type="password" class="form-control" name="new_password" id="new_password" placeholder=" " required>
                            <label for="new_password">New Password</label>
                        </div>
                        <div class="form-group position-relative">
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder=" " required>
                            <label for="confirm_password">Confirm New Password</label>
                        </div>
                        <button type="submit" class="btn-submit">Change Password</button>
                        <div class="small-text mt-3 d-flex">
                        <p>Remembered your password? <a href="sign-in.php">Log in</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer mt-auto">
        <p>&copy; 2024 Dine&Watch. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script>
        window.onload = function() {
            <?php if ($message): ?>
                Swal.fire({
                    title: '<?php echo ucfirst($messageType); ?>',
                    text: '<?php echo $message; ?>',
                    icon: '<?php echo $messageType; ?>'
                }).then(() => {
                    <?php if ($messageType === 'success'): ?>
                        window.location.href = './../sign-in.php';
                    <?php endif; ?>
                });
            <?php endif; ?>
        };
    </script>
</body>
</html>

