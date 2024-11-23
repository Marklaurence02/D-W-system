<?php
  include 'assets/process_signup.php'; 
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
        max-width: 500px;
        margin: 0 auto;
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

    input {
        padding-top: 20px;
    }

    input:focus + label,
    input:not(:placeholder-shown) + label {
        top: -10px;
        font-size: 12px;
        color: #ff6700;
    }

    input:focus::placeholder,
    input:not(:placeholder-shown)::placeholder {
        visibility: hidden;
    }

    .password-container {
    position: relative;
}

.eye-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    transition: all 0.3s ease;
}

input[type="password"] {
    transition: all 0.3s ease;
}
    .btn-next, .btn-back {
        background-color: #333;
        color: white;
        padding: 15px;
        font-size: 1.1rem;
        border-radius: 8px;
        border: none;
        margin-top: 20px;
        width: 48%;
    }

    .btn-next:hover, .btn-back:hover {
        background-color: #ff6700;
        color: white;
    }

    .btn-group {
        display: flex;
        justify-content: space-between;
        gap: 10px;
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

    .input-group {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .input-group input {
        flex: 1;
    }

    .form-control {
        border-radius: 8px;
        padding: 12px;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #ff6700;
    }

    /* Initially hide the personal info section */
    .personal-info {
        display: none;
    }

    .error-message {
        color: red;
        font-size: 0.875rem;
        margin-top: 5px;
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
                <img src="Images/logo.png" alt="Dine&Watch Logo" class="logo-container img-fluid">
            </div>

            <!-- Sign-Up Form Section -->
            <div class="col-12 col-md-6">
                <div class="signup-card">
                    <h2>Sign Up</h2>
                    <?php 
                    // Display any error messages
                    if (!empty($error)): 
                ?>
                    <p style="color:red;"><?= htmlspecialchars($error); ?></p>
                <?php endif; ?>

                    <!-- Sign-Up Form -->
                    <form action="" method="POST">
                        <!-- Account Information -->
                        <div class="form-group" id="emailField">
                            <input type="email" class="form-control" name="email" placeholder="Email" required value="<?= htmlspecialchars($email ?? ''); ?>">
                            <label for="email">Email</label>
                        </div>

                        <div class="form-group password-container" id="passwordField">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                            <label for="password">Password</label>
                            <span id="togglePassword" class="eye-icon">
                                <box-icon id="hideIcon" name="hide" type="solid"></box-icon>
                                <box-icon id="showIcon" name="show" type="solid" style="display: none;"></box-icon>
                            </span>
                        </div>

                        <div class="form-group password-container" id="confirmPasswordField">
                            <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password" required>
                            <label for="confirmPassword">Confirm Password</label>
                            <span id="toggleConfirmPassword" class="eye-icon">
                                <box-icon id="hideIconConfirm" name="hide" type="solid"></box-icon>
                                <box-icon id="showIconConfirm" name="show" type="solid" style="display: none;"></box-icon>
                            </span>
                            <div id="errorMessage" class="error-message" style="display: none;">Passwords do not match.</div>
                        </div>

                        <!-- Button to show personal info -->
                        <button type="button" id="nextBtn" class="btn-next">Next</button>

                        <!-- Personal Information (Initially hidden) -->
                        <div class="personal-info mt-4">
                            <div class="form-group" id="firstNameField">
                                <input type="text" class="form-control" name="firstName" placeholder="First Name" required value="<?= htmlspecialchars($firstName ?? ''); ?>">
                                <label for="firstName">First Name</label>
                            </div>

                            <div class="input-group">
                                <input type="text" class="form-control" name="middleInitial" placeholder="Middle Initial" value="<?= htmlspecialchars($middleInitial ?? ''); ?>">
                                <input type="text" class="form-control" name="suffix" placeholder="Suffix (Optional)" value="<?= htmlspecialchars($suffix ?? ''); ?>">
                            </div>

                            <div class="form-group" id="lastNameField">
                                <input type="text" class="form-control" name="lastName" placeholder="Last Name" required value="<?= htmlspecialchars($lastName ?? ''); ?>">
                                <label for="lastName">Last Name</label>
                            </div>

                            <div class="form-group">
                                <input type="text" class="form-control" name="address" placeholder="Address" required value="<?= htmlspecialchars($address ?? ''); ?>">
                                <label for="address">Address</label>
                            </div>

                            <div class="form-group">
                                <input type="tel" class="form-control" name="phone" placeholder="Phone Number" required value="<?= htmlspecialchars($phone ?? ''); ?>" pattern="09[0-9]{9}" title="Contact number must start with 09 and have exactly 11 digits.">
                                <label for="phone">Phone Number</label>
                            </div>

                            <div class="form-group">
                                <input type="text" class="form-control" name="zipCode" placeholder="Zip Code" required value="<?= htmlspecialchars($zipCode ?? ''); ?>">
                                <label for="zipCode">Zip Code</label>
                            </div>

                            <div class="btn-group">
                                <button type="button" id="backBtn" class="btn-back">Back</button>
                                <button type="submit" class="btn-next">Sign Up</button>
                            </div>

                        </div>
                    </form>
                    <p class="text-center">Already have an account? <a href="sign-in.php">Sign-in here</a></p>

                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 Dine&Watch. All rights reserved.</p>
    </footer>

   <!-- Bootstrap JS, Boxicons JS -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <!-- Password Toggle Script -->
    <script>
 // Password toggle functionality
const togglePassword = document.querySelector("#togglePassword");
const passwordField = document.querySelector("#password");

togglePassword.addEventListener("click", function () {
    const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
    passwordField.setAttribute("type", type);
    
    // Smoothly toggle the visibility of icons
    document.querySelector("#showIcon").style.display = type === "password" ? "none" : "inline";
    document.querySelector("#hideIcon").style.display = type === "password" ? "inline" : "none";

    // Apply smooth transition to icon toggle
    this.style.transition = "all 0.3s ease";
});

const toggleConfirmPassword = document.querySelector("#toggleConfirmPassword");
const confirmPasswordField = document.querySelector("#confirmPassword");

toggleConfirmPassword.addEventListener("click", function () {
    const type = confirmPasswordField.getAttribute("type") === "password" ? "text" : "password";
    confirmPasswordField.setAttribute("type", type);

    // Smoothly toggle the visibility of icons
    document.querySelector("#showIconConfirm").style.display = type === "password" ? "none" : "inline";
    document.querySelector("#hideIconConfirm").style.display = type === "password" ? "inline" : "none";
    
    // Apply smooth transition to icon toggle
    this.style.transition = "all 0.3s ease";
});

        // Show personal info when "Next" is clicked
        document.getElementById("nextBtn").addEventListener("click", function () {
            // Hide account information fields
            document.getElementById("emailField").style.display = "none";
            document.getElementById("passwordField").style.display = "none";
            document.getElementById("confirmPasswordField").style.display = "none";

            // Show personal information section
            document.querySelector(".personal-info").style.display = "block";

            // Hide the "Next" button
            this.style.display = "none";
        });

        // Hide personal info and show account info when "Back" is clicked
        document.getElementById("backBtn").addEventListener("click", function () {
            // Show account information fields again
            document.getElementById("emailField").style.display = "block";
            document.getElementById("passwordField").style.display = "block";
            document.getElementById("confirmPasswordField").style.display = "block";

            // Hide personal information section
            document.querySelector(".personal-info").style.display = "none";

            // Show the "Next" button again
            document.getElementById("nextBtn").style.display = "block";
        });
    </script>

</body>

</html>
