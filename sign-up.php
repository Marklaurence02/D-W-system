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
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        font-family: 'Segoe UI', Arial, sans-serif;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .header {
        background: linear-gradient(90deg, #ff6700 0%, #ff8533 100%);
        padding: 15px 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .header h1 {
        font-size: 2.2rem;
        letter-spacing: 1px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }

    .footer {
        background: linear-gradient(90deg, #ff6700 0%, #ff8533 100%);
        margin-top: auto;
        padding: 20px 0;
    }

    .logo-container {
        max-width: 350px;
        margin: 0 auto;
    }

    .signup-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        padding: 35px;
        transition: transform 0.3s ease;
        max-width: 500px;
        margin: 0 auto;
    }

    .signup-card:hover {
        transform: translateY(-5px);
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
        background: linear-gradient(90deg, #ff6700 0%, #ff8533 100%);
        border-radius: 12px;
        padding: 12px 25px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255,103,0,0.2);
        color: white;
        border: none;
        margin-top: 20px;
        width: 100%;
    }

    .btn-next:hover, .btn-back:hover {
        background: linear-gradient(90deg, #ff8533 0%, #ff6700 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255,103,0,0.3);
    }

    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .btn-group button {
        width: 100%;
        margin: 0;
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
        flex-direction: row;
        gap: 10px;
        margin-bottom: 20px;
    }

    .input-group input {
        position: relative;
        width: 50%;
    }

    .input-group input:first-child {
        flex: 1;
        max-width: 30%;
    }

    .input-group input:last-child {
        flex: 2;
        max-width: 70%;
    }

    .input-group input + label {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
        font-size: 16px;
        transition: all 0.3s ease;
        pointer-events: none;
    }

    .input-group input:focus + label,
    .input-group input:not(:placeholder-shown) + label {
        top: -10px;
        font-size: 12px;
        color: #ff6700;
    }

    .form-control {
        border: 2px solid #e9ecef;
        padding: 12px 15px;
        border-radius: 12px;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }

    .form-control:focus {
        border-color: #ff6700;
        background: white;
        box-shadow: 0 0 0 3px rgba(255,103,0,0.1);
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
                            <input type="email" class="form-control" name="email" placeholder="" required value="<?= htmlspecialchars($email ?? ''); ?>">
                            <label for="email">Email</label>
                        </div>

                        <div class="form-group password-container" id="passwordField">
                            <input type="password" class="form-control" name="password" id="password" placeholder="" required pattern=".{8,}" title="Password must be at least 8 characters long">
                            <label for="password">Password</label>
                            <span id="togglePassword" class="eye-icon">
                                <box-icon id="hideIcon" name="hide" type="solid"></box-icon>
                                <box-icon id="showIcon" name="show" type="solid" style="display: none;"></box-icon>
                            </span>
                        </div>

                        <div class="form-group password-container" id="confirmPasswordField">
                            <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder=" " required>
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
                                <input type="text" class="form-control" name="firstName" placeholder=" " required value="<?= htmlspecialchars($firstName ?? ''); ?>">
                                <label for="firstName">First Name</label>
                            </div>

                            <div class="input-group">
                                <input type="text" class="form-control" name="middleInitial" placeholder="" value="<?= htmlspecialchars($middleInitial ?? ''); ?>" style="flex: 1; max-width: 70%;">
                                <label for="middleInitial">Middile Initial</label>
                                <input type="text" class="form-control" name="suffix" placeholder="Suffix(Optional) " value="<?= htmlspecialchars($suffix ?? ''); ?>" style="flex: 2; max-width: 30%;">
                            </div>

                            <div class="form-group" id="lastNameField">
                                <input type="text" class="form-control" name="lastName" placeholder=" " required value="<?= htmlspecialchars($lastName ?? ''); ?>">
                                <label for="lastName">Last Name</label>
                            </div>

                            <div class="form-group">
                                <input type="text" class="form-control" name="address" placeholder="" required value="<?= htmlspecialchars($address ?? ''); ?>">
                                <label for="address">Address</label>
                            </div>

                            <div class="form-group">
                                <input type="tel" class="form-control" name="phone" placeholder=" " required value="<?= htmlspecialchars($phone ?? ''); ?>" pattern="09[0-9]{9}" title="Contact number must start with 09 and have exactly 11 digits.">
                                <label for="phone">Phone Number</label>
                            </div>

                            <div class="form-group">
                                <input type="text" class="form-control" name="zipCode" placeholder=" " required 
                                    minlength="4" maxlength="5" pattern="[0-9]{4,5}" title="Zip code must be 4 or 5 digits"
                                    value="<?= htmlspecialchars($zipCode ?? ''); ?>">
                                <label for="zipCode">Zip Code</label>
                            </div>

                            <div class="btn-group" style="flex-direction: row; gap: 10px;">
                                <button type="button" id="backBtn" class="btn-back">Back</button>
                                <button type="submit" class="btn-next">Sign Up</button>
                            </div>

                        </div>
                    </form>
                    <div class="small-text mt-3 d-flex"><p class="text-center">Already have an account? <a href="sign-in.php">Sign-in here</a></p>
                    </div>

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

        // Add password confirmation validation
        const password = document.querySelector("#password");
        const confirmPassword = document.querySelector("#confirmPassword");
        const errorMessage = document.querySelector("#errorMessage");
        const nextBtn = document.querySelector("#nextBtn");

        function validatePasswords() {
            // Check if either field is empty or password is too short
            if (!password.value || !confirmPassword.value) {
                errorMessage.style.display = "none";
                nextBtn.disabled = true;
                nextBtn.style.opacity = "0.5";
                return false;
            }
            
            // Check password length
            if (password.value.length < 8) {
                errorMessage.textContent = "Password must be at least 8 characters long";
                errorMessage.style.display = "block";
                nextBtn.disabled = true;
                nextBtn.style.opacity = "0.5";
                return false;
            }
            
            // Check if passwords match
            if (password.value !== confirmPassword.value) {
                errorMessage.textContent = "Passwords do not match";
                errorMessage.style.display = "block";
                nextBtn.disabled = true;
                nextBtn.style.opacity = "0.5";
                return false;
            }
            
            errorMessage.style.display = "none";
            nextBtn.disabled = false;
            nextBtn.style.opacity = "1";
            return true;
        }

        // Add event listeners for password validation
        password.addEventListener("input", validatePasswords);
        confirmPassword.addEventListener("input", validatePasswords);

        // Modify the Next button click handler
        document.getElementById("nextBtn").addEventListener("click", function() {
            if (!validatePasswords()) {
                return; // Don't proceed if validation fails
            }
            
            // Existing code for showing/hiding elements
            document.getElementById("emailField").style.display = "none";
            document.getElementById("passwordField").style.display = "none";
            document.getElementById("confirmPasswordField").style.display = "none";
            document.querySelector(".personal-info").style.display = "block";
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

    <!-- Include SweetAlert CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>
