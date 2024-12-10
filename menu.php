<?php
$config = include 'assets/config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch menu items with categories
$sql = "SELECT 
            p.product_id, 
            p.product_name, 
            p.price, 
            p.special_instructions, 
            p.product_image, 
            p.quantity, 
            c.category_name 
        FROM 
            product_items p 
        JOIN 
            product_categories c 
        ON 
            p.category_id = c.category_id 
        ORDER BY 
            c.category_name, p.product_name";

$result = $conn->query($sql);

if (!$result) {
    die("Query Error: " . $conn->error);
}

// Organize items by category
$menu = [];
while ($row = $result->fetch_assoc()) {
    $menu[$row['category_name']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Menu - Dine&Watch</title>
    <link rel="icon" type="image/png" href="images/icon1.png">

    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <header class="custom-header">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="logo">
                <img src="Images/dinewatchlogo.png" alt="Dine & Watch Logo" class="logo-img">
            </div>
            <!-- Navigation links for larger screens -->
            <nav class="nav-menu d-none d-lg-flex"> <!-- Visible only on large screens and above -->
                <a href="index.php"><i class="fa fa-home"></i> Home</a>
                <a href="#" class="active"><i class="fa fa-utensils"></i> Menu</a>
                <a href="index.php#premium-pizza-section"><i class="fa fa-star"></i> Premiums</a>
                <a href="index.php#contact-section"><i class="fa fa-phone"></i> Contact Us</a>
            </nav>
            <!-- Sidebar toggle button for smaller screens -->
            <button class="openbtn d-lg-none" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
        </div>
    </header>

    <!-- Sidebar structure -->
    <div id="mySidebar" class="sidebar">
        <a href="javascript:void(0)" class="closebtn" onclick="toggleSidebar()">&times;</a>
        <a href="index.php"><i class="fa fa-home"></i> Home</a>
        <a href="#" class="active"><i class="fa fa-utensils"></i> Menu</a>
        <a href="landing_page.php#premium-pizza-section"><i class="fa fa-star"></i> Premiums</a>
        <a href="landing_page.php#contact-section"><i class="fa fa-phone"></i> Contact Us</a>
    </div>

    <!-- Main content -->
    <div id="mainContent">
        <!-- Menu Section -->
        <section class="menu-section py-5">
            <div class="container">
                <h1 class="text-center mb-5 fw-bold">Our Menu</h1>

                <!-- Display Categories -->
                <?php foreach ($menu as $category => $items): ?>
                    <h2 class="fw-bold text-center mb-4"><?php echo htmlspecialchars($category); ?></h2>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        <?php foreach ($items as $item): ?>
                            <div class="col">
                                <div class="card shadow-sm h-100 custom-card">
                                    <div class="row g-0">
                                        <div class="col-4">
                                            <img src="Images/<?php echo htmlspecialchars($item['product_image']); ?>" 
                                                class="img-fluid rounded-start"
                                                alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                        </div>
                                        <div class="col-8">
                                            <div class="card-body">
                                                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($item['product_name']); ?></h5>
                                                <p class="card-text text-danger fw-bold">₱<?php echo number_format($item['price'], 2); ?></p>
                                                <p class="card-text"><?php echo htmlspecialchars($item['special_instructions']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <footer class="footer py-3">
            <div class="container">
                <p class="mb-0">2024 - Made by Sound System Group - BSU - TNEU - Lipa Campus</p>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("mySidebar");
            const mainContent = document.getElementById("mainContent");

            if (sidebar) {
                const isOpen = sidebar.style.width !== "0px";
                sidebar.style.width = isOpen ? "0" : "auto";
                if (mainContent) {
                    mainContent.style.marginRight = isOpen ? "0" : `${sidebar.scrollWidth}px`;
                }
            } else {
                console.error("Sidebar element not found");
            }
        }
    </script>
</body>
</html>
