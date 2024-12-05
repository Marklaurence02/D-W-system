<?php
include_once "assets/config.php"; // Database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/Opanel.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
           #Sidebar{
            height:auto;
        }
        .card {
            
            background-color: #ADADAD;
            border: none;
            border-radius: 10px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .card i {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .text-title {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .list-group-item {
            border: none;
            padding: 0.75rem 1.25rem;
            background-color: #ffffff;
            margin-bottom: 0.5rem;
            border-radius: 5px;
        }
        /* Add padding to account for fixed header */
        body {
            padding-top: 60px;
        }

        /* Update main content area */
        #main-content {
            margin-left: 250px; /* Match sidebar width */
            transition: margin-left 0.3s ease;
            padding: 20px;
            min-height: calc(100vh - 60px); /* Subtract header height */
        }

        /* Adjust main content when sidebar is collapsed */
        #main-content.sidebar-collapsed {
            margin-left: 70px; /* Match collapsed sidebar width */
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #main-content {
                margin-left: 0;
            }
            
            #main-content.sidebar-collapsed {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
<?php
        include "Header_nav/adminHeader.php"; // Header for admin panel
    ?>

    <div class="d-flex" style="min-height: 100vh; overflow-x: hidden;">
        <!-- Sidebar -->
        <div id="Sidebar" class="flex-shrink-0">
            <?php include "Header_nav/ad-sidebar.php"; ?>
        </div>
        <div id="main-content" class="container-fluid allContent-section py-4 text-center">
            <h1 class="text-center">Dashboard</h1>
            <div class="row" style="background-color: #ADADAD; border-radius: 10px; margin: 20px; padding: 10px;">
                <!-- Total Sales -->
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="card text-center text-white shadow-sm" style="background-color: #5cb85c; border-radius: 10px;">
                        <div class="card-body">
                            <i class="fa fa-dollar"></i>
                            <p class="text-title">Total Sales</p>
                            <h5 id="totalSales">
                                <?php
                                    $sql = "SELECT SUM(orders.total_amount) AS total_sales 
                                            FROM orders 
                                            WHERE orders.status IN ('paid in advance', 'completed')";
                                    $result = $conn->query($sql);
                                    if ($result && $row = $result->fetch_assoc()) {
                                        $totalSales = $row['total_sales'] ?? 0;
                                        echo '&#x20B1;' . number_format($totalSales, 2);
                                    } else {
                                        echo '&#x20B1;0.00';
                                    }
                                ?>
                            </h5>
                        </div>
                    </div>
                </div>

                <!-- Total Orders -->
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="card text-center text-white shadow-sm" style="background-color: #f0ad4e; border-radius: 10px;">
                        <div class="card-body">
                            <i class="fa fa-list"></i>
                            <p class="text-title">Total Orders</p>
                            <h5 id="totalOrders">
                                <?php
                                    $sql = "SELECT COUNT(orders.order_id) AS total_orders FROM orders WHERE orders.status != 'Canceled'";
                                    $result = $conn->query($sql);
                                    echo $result && $row = $result->fetch_assoc() ? $row['total_orders'] ?? 0 : '0';
                                ?>
                            </h5>
                        </div>
                    </div>
                </div>

                <!-- Total Sold -->
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="card text-center text-white shadow-sm" style="background-color: #5bc0de; border-radius: 10px;">
                        <div class="card-body">
                            <i class="fa fa-bar-chart"></i>
                            <p class="text-title">Total Sold</p>
                            <h5 id="totalSold">
                                <?php
                                    $sql = "
                                        SELECT SUM(receipt_items.item_total_price) AS total_sold 
                                        FROM receipt_items 
                                        JOIN receipts ON receipt_items.receipt_id = receipts.receipt_id 
                                        JOIN orders ON receipts.order_id = orders.order_id 
                                        WHERE orders.status IN ('Completed', 'paid in advance')
                                    ";
                                    $result = $conn->query($sql);
                                    echo '&#x20B1;' . ($result && $row = $result->fetch_assoc() ? number_format($row['total_sold'] ?? 0, 2) : '0.00');
                                ?>
                            </h5>
                        </div>
                    </div>
                </div>

                <!-- Total Customers -->
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="card text-center text-white shadow-sm" style="background-color: #337ab7; border-radius: 10px;">
                        <div class="card-body">
                            <i class="fa fa-users"></i>
                            <p class="text-title">Total Customers</p>
                            <h5 id="totalCustomers">
                                <?php
                                    $sql = "SELECT COUNT(DISTINCT orders.user_id) AS total_customers FROM orders";
                                    $result = $conn->query($sql);
                                    echo $result && $row = $result->fetch_assoc() ? $row['total_customers'] ?? 0 : '0';
                                ?>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row mt-5">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Sales Trend</h5>
                            <canvas id="salesTrendChart"></canvas>
                        </div>
                    </div>

                    <div class="card shadow-sm mt-4">
                        <div class="card-body">
                            <h5 class="card-title">Orders Distribution</h5>
                            <canvas id="ordersDistributionChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Popular Products Section -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-3">
                            <h5 class="card-title">Popular Products</h5>
                            <ul class="list-group">
                                <?php
                                    $sql = "
                                        SELECT product_items.product_name, product_items.product_image, SUM(receipt_items.quantity) AS total_sold 
                                        FROM receipt_items 
                                        JOIN product_items ON receipt_items.product_id = product_items.product_id 
                                        GROUP BY product_items.product_name, product_items.product_image 
                                        ORDER BY total_sold DESC 
                                        LIMIT 5
                                    ";
                                    $result = $conn->query($sql);
                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $imagePath = $row['product_image'] ? htmlspecialchars($row['product_image']) : '../uploads/default-placeholder.jpg';
                                            echo "<li class='list-group-item d-flex align-items-center py-3'>
                                                    <img src='$imagePath' alt='Product Image' class='rounded-circle' style='width: 50px; height: 50px; object-fit: cover; margin-right: 15px;'>
                                                    <div>
                                                        <strong>" . htmlspecialchars($row['product_name']) . "</strong>
                                                        <small class='d-block text-muted'>" . $row['total_sold'] . " sold</small>
                                                    </div>
                                                  </li>";
                                        }
                                    } else {
                                        echo "<li class='list-group-item'>No popular products available.</li>";
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Popular Reserved Tables Section -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-3">
                            <h5 class="card-title">All Reserved Tables</h5>
                            <ul class="list-group">
                                <?php
                                    $sql = "
                                        SELECT 
                                            tables.table_number, 
                                            tables.table_id, 
                                            COUNT(DISTINCT reservations.reservation_id) AS reservation_count,
                                            MIN(table_images.image_path) AS image_path
                                        FROM reservations
                                        JOIN tables ON reservations.table_id = tables.table_id
                                        LEFT JOIN table_images ON tables.table_id = table_images.table_id
                                        WHERE reservations.status IN ('Complete', 'Reserved') 
                                        GROUP BY tables.table_id
                                        ORDER BY reservation_count DESC
                                    ";

                                    $result = $conn->query($sql);
                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $imagePath = $row['image_path'] ? htmlspecialchars($row['image_path']) : '../uploads/default-placeholder.jpg';
                                            echo "<li class='list-group-item d-flex align-items-center py-3'>
                                                    <img src='$imagePath' alt='Table Image' class='rounded-circle' style='width: 50px; height: 50px; object-fit: cover; margin-right: 15px;'>
                                                    <div>
                                                        <strong>Table #" . htmlspecialchars($row['table_number']) . "</strong>
                                                        <small class='d-block text-muted'>" . $row['reservation_count'] . " Reservation(s)</small>
                                                    </div>
                                                  </li>";
                                        }
                                    } else {
                                        echo "<li class='list-group-item'>No reserved tables available.</li>";
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Recent Feedback Section -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-3">
                            <h5 class="card-title">Recent Feedback</h5>
                            <ul class="list-group">
                                <?php
                                    $sql = "
                                        SELECT 
                                            feedbacks.feedback_text,
                                            feedbacks.rating,
                                            feedbacks.created_at,
                                            users.first_name,
                                            users.last_name,
                                            users.username
                                        FROM feedbacks
                                        JOIN users ON feedbacks.user_id = users.user_id
                                        ORDER BY feedbacks.created_at DESC
                                        LIMIT 3
                                    ";

                                    $result = $conn->query($sql);
                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $userFullName = htmlspecialchars($row['first_name'] . " " . $row['last_name']);
                                            $username = htmlspecialchars($row['username']);
                                            $feedbackText = htmlspecialchars($row['feedback_text']);
                                            $rating = intval($row['rating']);
                                            $createdAt = date("F j, Y, g:i a", strtotime($row['created_at']));

                                            echo "
                                                <li class='list-group-item py-3'>
                                                    <div class='d-flex justify-content-between align-items-center'>
                                                        <div>
                                                            <strong>$userFullName (@$username)</strong>
                                                            <small class='text-muted d-block'>$createdAt</small>
                                                            <p class='mb-1'>$feedbackText</p>
                                                        </div>
                                                        <div>
                                                            <span class='badge bg-primary rounded-pill'>$rating ★</span>
                                                        </div>
                                                    </div>
                                                </li>
                                            ";
                                        }
                                    } else {
                                        echo "<li class='list-group-item'>No feedback available.</li>";
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="Js/sales_chart.js"></script>
    <script src="Js/panel.js"></script>
    <script src="Js/navb.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css" />

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>


<!-- generate report -->
 <!-- Include jQuery -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">




<!-- Include DataTables Buttons extension CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
</body>
</html>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<!-- Add SweetAlert2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

fetch('../assets/fetch_data.php')
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Extract data with default fallbacks
        const chartLabels = data.chartLabels ?? [];
        const chartData = data.chartData ?? [];
        const totalSales = data.totalSales ?? 0;
        const totalOrders = data.totalOrders ?? 0;
        const totalSold = data.totalSold ?? 0;
        const totalCustomers = data.totalCustomers ?? 0;
        const orderStatuses = data.orderStatuses ?? [];
        const orderCounts = data.orderCounts ?? [];

        // Display values in HTML
        document.getElementById('totalSales').innerText = '₱' + totalSales.toLocaleString('en-US');
        document.getElementById('totalOrders').innerText = totalOrders.toLocaleString('en-US');
        document.getElementById('totalSold').innerText = totalSold.toLocaleString('en-US');
        document.getElementById('totalCustomers').innerText = totalCustomers.toLocaleString('en-US');

        // Create Sales Trend chart using Chart.js
        const trendCtx = document.getElementById('salesTrendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Sales Trend (₱)',
                    data: chartData,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    tension: 0.3, // Smooth curve
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Sales Amount (₱)'
                        },
                        beginAtZero: true
                    }
                },
                plugins: {
                    afterDraw: (chart) => {
                        const ctx = chart.ctx;
                        ctx.save();
                        ctx.font = '14px Arial';
                        ctx.textAlign = 'center';
                        ctx.fillStyle = 'gray';

                        // Display current date at the bottom of the chart
                        const currentDate = new Date();
                        const formattedDate = currentDate.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                        const chartWidth = chart.width;
                        const chartHeight = chart.height;
                        ctx.fillText(
                            `Date: ${formattedDate}`,
                            chartWidth / 2,
                            chartHeight + chart.options.layout.padding.bottom - 10
                        );
                        ctx.restore();
                    }
                },
                layout: {
                    padding: {
                        bottom: 40 // Ensure space for the date text
                    }
                }
            }
        });

        // Create Orders Distribution chart using Chart.js
        const ordersCtx = document.getElementById('ordersDistributionChart').getContext('2d');
        new Chart(ordersCtx, {
            type: 'bar',
            data: {
                labels: orderStatuses, // Array of order statuses
                datasets: [{
                    label: 'Number of Orders',
                    data: orderCounts, // Array of counts for each status
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Order Status'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Orders'
                        }
                    }
                }
            }
        });
    })
    .catch(error => console.error('Error fetching data:', error));


</script>
