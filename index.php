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
            display: flex;
            flex-direction: column;
            height: 100vh; /* Full viewport height */
            margin: 0;
        }
        .header {
            background-color: #ff6700; /* Orange header */
            color: white;
            padding: 20px 40px;
        }
        .header h1 {
            margin: 0;
            font-weight: bold;
        }
        .footer {
            background-color: #ff6700;
            color: white;
            padding: 15px 20px;
            text-align: center;
            font-size: 0.9rem;
            margin-top: auto; /* Pushes the footer to the bottom */
        }
        .logo-container {
            max-width: 350px;
        }
        .page {
            text-align: center;
            padding: 50px 0;
            flex-grow: 1; /* Ensures the content takes the remaining space */
        }
        .btn {
            margin-top: 30px;
        }
        .btn-regester, .btn-log-in {
            background-color: #333;
            color: white;
            padding: 15px 30px;
            font-size: 1rem;
            border-radius: 8px;
            border: none;
            width: 200px;
            margin: 10px;
        }
        .btn-regester:hover, .btn-log-in:hover {
            background-color: #ff6700;
        }
        .small-text {
            font-size: 0.9rem;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header d-flex align-items-center">
        <h1>DINE&WATCH</h1>
    </header>

  <!-- Main Content -->
  <div class="container page">
        <!-- Row for Logo and Buttons -->
        <div class="row d-flex justify-content-between align-items-center">
            <!-- Logo Section -->
            <div class="col-md-4 text-center">
                <img src="Images/logo.png" alt="Dine&Watch Logo" class="logo-container img-fluid">
            </div>

            <!-- Buttons for Sign-Up and Log-In -->
            <div class="col-md-4 text-center">
                <a href="sign-up.php"><button class="btn-regester">Sign-Up</button></a>
                <a href="sign-in.php"><button class="btn-log-in">Log-In</button></a>
            </div>
        </div>

        <?php if (isset($_GET['signup']) && $_GET['signup'] === 'success'): ?>
        <script>
            // Display the alert message
            alert("Your account has been created successfully! You can now log in.");
        </script>
        <?php endif; ?>
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
