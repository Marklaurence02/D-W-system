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
    <link rel="stylesheet" href="css/panel.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
    <style>
        body {
            background-color: #f8f9fa; /* Light background */
        }
        .card {
            border: none; /* Remove border */
            border-radius: 10px; /* Rounded corners */
            transition: transform 0.3s, box-shadow 0.3s; /* Smooth hover effects */
        }
        .card:hover {
            transform: translateY(-5px); /* Lift effect */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Shadow */
        }
        .card i {
            font-size: 60px;
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
    </style>
</head>
<body>
    <?php
        include "Header_nav/ownerHeader.php"; // Header for admin panel
        include "Header_nav/own-sidebar.php"; // Sidebar for navigation
    ?>

    <div id="main-content" class="container allContent-section py-4">
        <div class="row">
            <!-- Total Sales -->
            <div class="col-sm-3 mb-4">
                <div class="card text-center bg-success text-white shadow-sm">
                    <div class="card-body">
                        <i class="fa fa-dollar"></i>
                        <p class="text-title">Total Sales</p>
                        <h5 id="totalSales">
                            <?php
                                $sql = "SELECT SUM(orders.total_amount) AS total_sales FROM orders WHERE orders.status IN ('Completed', 'paid in advance')";
                                $result = $conn->query($sql);
                                echo '&#x20B1;' . ($result && $row = $result->fetch_assoc() ? number_format($row['total_sales'] ?? 0, 2) : '0.00');
                            ?>
                        </h5>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="col-sm-3 mb-4">
                <div class="card text-center bg-warning text-white shadow-sm">
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
            <div class="col-sm-3 mb-4">
                <div class="card text-center bg-info text-white shadow-sm">
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
            <div class="col-sm-3 mb-4">
                <div class="card text-center bg-primary text-white shadow-sm">
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
        <canvas id="salesTrendChart" width="600" height="300"></canvas>
    </div>
</div>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Orders Distribution</h5>
                        <canvas id="ordersDistributionChart" width="600" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Popular Products -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Recent Activity</h5>
                        <ul class="list-group">
                            <?php
                                $sql = "SELECT action_details, created_at FROM activity_logs ORDER BY created_at DESC LIMIT 5";
                                $result = $conn->query($sql);
                                if ($result) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<li class='list-group-item'>" . htmlspecialchars($row['action_details']) . " - " . date('Y-m-d H:i', strtotime($row['created_at'])) . "</li>";
                                    }
                                }
                            ?>
                        </ul>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Popular Products</h5>
                        <ul class="list-group">
                            <?php
                                $sql = "
                                    SELECT product_items.product_name, SUM(receipt_items.quantity) AS total_sold 
                                    FROM receipt_items 
                                    JOIN product_items ON receipt_items.product_id = product_items.product_id 
                                    GROUP BY product_items.product_name 
                                    ORDER BY total_sold DESC 
                                    LIMIT 5
                                ";
                                $result = $conn->query($sql);
                                if ($result) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<li class='list-group-item'>" . htmlspecialchars($row['product_name']) . " - " . $row['total_sold'] . " sold</li>";
                                    }
                                }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Loading Order and Dependencies -->
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="Js/sales_chart.js"></script>
    <script src="Js/Ownerpanel.js"></script>
    <script src="Js/navb.js"></script>

    <script>
    fetch('assets/fetch_data.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const chartLabels = data.chartLabels ?? [];
            const chartData = data.chartData ?? [];
            const totalSales = data.totalSales ?? 0;
            const totalOrders = data.totalOrders ?? 0;
            const totalSold = data.totalSold ?? 0;
            const totalCustomers = data.totalCustomers ?? 0;

            // Display values in HTML
            document.getElementById('totalSales').innerText = '₱' + totalSales.toLocaleString();
            document.getElementById('totalOrders').innerText = totalOrders;
            document.getElementById('totalSold').innerText = totalSold;
            document.getElementById('totalCustomers').innerText = totalCustomers;

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
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching data:', error));
</script>

</body>
</html>
