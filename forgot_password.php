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
    display: flex;
    flex-direction: column;
    min-height: 100vh;
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
    margin-top: auto;  /* Pushes footer to the bottom */
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
                <img src="Images/logo.png" alt="Dine&Watch Logo" class="logo-container img-fluid">
            </div>

            <!-- OTP Form Section -->
            <div class="col-12 col-md-6">
                <div class="signup-card mx-auto" id="otp-card">
                    <h2>Forgot Password</h2>

                    <form id="otp-form" onsubmit="sendOTP(event)">
                        <!-- Input for Phone Number -->
                        <div class="form-group position-relative" id="input-container">
                            <input type="tel" class="form-control" name="contact" id="contact" placeholder=" " required>
                            <label for="contact">Enter your Phone Number</label>
                        </div>

                        <button type="submit" class="btn-next">Send OTP</button>
                    </form>

                    <div class="small-text">
                        <p>Already have an account? <a href="sign-in.php">Log in here</a></p>
                    </div>
                </div>

                <!-- New Password Section (hidden initially) -->
                <div class="signup-card mx-auto" id="new-pass-card" style="display: none;">
                    <h2>Create New Password</h2>

                    <form id="new-password-form" onsubmit="submitNewPassword(event)">
                        <!-- New Password Input -->
                        <div class="form-group position-relative">
                            <input type="password" class="form-control" name="new-password" id="new-password" placeholder=" " required>
                            <label for="new-password">Enter New Password</label>
                        </div>

                        <!-- Confirm Password Input -->
                        <div class="form-group position-relative">
                            <input type="password" class="form-control" name="confirm-password" id="confirm-password" placeholder=" " required>
                            <label for="confirm-password">Confirm New Password</label>
                        </div>

                        <button type="submit" class="btn-next">Set New Password</button>
                    </form>

                    <div class="small-text">
                        <p>Remember your password? <a href="sign-in.php">Log in here</a></p>
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

    <script>
        function sendOTP(event) {
            event.preventDefault(); // Prevent form submissionF

            const contact = document.getElementById('contact').value;

            if (!contact) {
                alert('Please provide your phone number.');
                return;
            }

            // Simulate OTP sent (replace with actual logic for SMS OTP)
            alert(`SMS OTP sent to ${contact}. Please check your phone.`);

            // Hide the OTP form and show the New Password form
            document.getElementById('otp-card').style.display = 'none';
            document.getElementById('new-pass-card').style.display = 'block';
        }

        function submitNewPassword(event) {
            event.preventDefault(); // Prevent form submission

            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            if (newPassword !== confirmPassword) {
                alert('Passwords do not match. Please try again.');
                return;
            }

            // Simulate password update (replace with actual logic to update password)
            alert('Your password has been successfully updated!');
        }
    </script>

</body>
</html>
