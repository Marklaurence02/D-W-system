<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dine&Watch</title>
    <link rel="stylesheet" href="css/Style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
</head>
<body>

    <div class="page">
        <div class="head">
            <div class="return">
                <a href="index.php"><img src="Images/back.png" alt="Go back"></a>
            </div>
        </div>
       
        <div class="S-section ">
            <div class="log-container">
                <h1>Sign-In</h1>

                <?php 
                    // Include the form processing logic
                    include 'assets/process_log-in.php'; 
                    
                    // Display any error messages
                    if (!empty($error)):  // Check if $error is not empty
                ?>
                    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p> <!-- Error message -->
                <?php endif; ?>

                <?php if (!empty($message)): ?>  <!-- Check if a message exists -->
                    <p style="color: green;">
                        <img src="Images/check-icon.png" alt="Success" style="width: 20px; vertical-align: middle;">
                        <?php echo htmlspecialchars($message); ?>
                    </p> <!-- Success message -->
                <?php endif; ?>

                <!-- Sign-In Form -->
                <form class="form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <input type="email" name="email" placeholder="Email" required>
                    
                    <!-- Password Field with Show/Hide toggle -->
                    <div class="password-container">
                        <input type="password" name="password" id="password" placeholder="Password" required>
                        
                        <!-- Use Boxicons for show/hide password -->
                        <span id="togglePassword" class="eye-icon">
                            <!-- Show Eye Icon (Initially Hidden) -->
                            <box-icon id="showIcon" name="show" type="solid" style="cursor: pointer;"></box-icon>
                            <!-- Hide Eye Icon (Initially Visible) -->
                            <box-icon id="hideIcon" name="hide" type="solid" style="cursor: pointer;"></box-icon>
                        </span>
                    </div>

                    <div class="forgot-password">
                        <a href="forgot_password.php">Forgot your password?</a>
                    </div>
                    <button type="submit">Sign In</button>
                </form>

            </div>
        </div>
    
        <footer class="footer"></footer>
    </div>

    <script>
        // Toggle Password Visibility
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#password');
        const showIcon = document.querySelector('#showIcon');
        const hideIcon = document.querySelector('#hideIcon');

        // Initially hide the "show" icon
        showIcon.style.display = 'none';

        togglePassword.addEventListener('click', function () {
            const isPasswordHidden = passwordField.getAttribute('type') === 'password';
            
            // Toggle the password field type
            passwordField.setAttribute('type', isPasswordHidden ? 'text' : 'password');
            
            // Show or hide the appropriate icon
            if (isPasswordHidden) {
                hideIcon.style.display = 'none';
                showIcon.style.display = 'inline';
            } else {
                hideIcon.style.display = 'inline';
                showIcon.style.display = 'none';
            }
        });
    </script>

</body>
</html>
