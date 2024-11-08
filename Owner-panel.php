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
        #main, #main-content {
  transition: margin-left 0.3s; /* Smooth transition */
}
        .card {
        background-color: #ADADAD; /* Set background color */
        border: none; /* Remove default border */
        border-radius: 10px; /* Rounded corners */
        transition: transform 0.3s, box-shadow 0.3s; /* Smooth hover effects */
    }
    .card:hover {
        transform: translateY(-5px); /* Lift effect on hover */
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Add shadow */
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

<div id="main-content" class="container allContent-section py-4 text-center">
        <h1 class=" text-center">Dashboard</h1>
        <div class="row" style="background-color: #ADADAD; border-radius: 10px; margin: 20px; padding: 10px;">
    <!-- Total Sales -->
    <div class="col-md-3 col-12 mb-4">
        <div class="card text-center text-white shadow-sm" style="background-color: #5cb85c; border-radius: 10px;">
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
    <div class="col-md-3 col-12 mb-4">
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
    <div class="col-md-3 col-12 mb-4">
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
    <div class="col-md-3 col-12 mb-4">
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
        <canvas id="salesTrendChart" width="600" height="300"></canvas>
    </div>
</div>


<div class="card shadow-sm mt-4">
    <div class="card-body">
        <h5 class="card-title">Orders Distribution</h5>
        <canvas id="ordersDistributionChart" width="600" height="300"></canvas>
    </div>
</div>
            </div>

           <!-- Recent Activity & Popular Products -->
           <div class="col-lg-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body p-3">
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
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            $imagePath = $row['product_image'] ? htmlspecialchars($row['product_image']) : '../uploads/default-placeholder.jpg';
                            echo "<li class='list-group-item d-flex align-items-center'>
                                    <img src='$imagePath' alt='Product Image' style='width: 50px; height: 50px; object-fit: cover; margin-right: 10px;'>
                                    <div>
                                        <strong>" . htmlspecialchars($row['product_name']) . "</strong> - " . $row['total_sold'] . " sold
                                    </div>
                                  </li>";
                        }
                    }
                ?>
            </ul>
        </div>
    </div>

<!-- Popular Reserved Tables -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <h5 class="card-title">All Reserved Tables</h5>
        <ul class="list-group">
            <?php
                // SQL Query to fetch the number of reservations for each table_id and associated image path
                $sql = "
                    SELECT 
                        tables.table_number, 
                        tables.table_id, 
                        COUNT(DISTINCT reservations.reservation_id) AS reservation_count,
                        MIN(table_images.image_path) AS image_path
                    FROM reservations
                    JOIN tables ON reservations.table_id = tables.table_id
                    LEFT JOIN table_images ON tables.table_id = table_images.table_id
                    WHERE reservations.status IN ('Complete', 'Paid') 
                    GROUP BY tables.table_id
                    ORDER BY reservation_count DESC
                ";

                // Execute the query
                $result = $conn->query($sql);

                if ($result) {
                    // Loop through the results and display table reservation counts
                    while ($row = $result->fetch_assoc()) {
                        // Check if the image path exists, otherwise use a placeholder image
                        $imagePath = $row['image_path'] ? htmlspecialchars($row['image_path']) : '../uploads/default-placeholder.jpg';
                        
                        // Log for debugging purposes
                        error_log("Table #" . htmlspecialchars($row['table_number']) . " has " . $row['reservation_count'] . " reservations.");

                        // Display each table reservation data
                        echo "<li class='list-group-item d-flex align-items-center'>
                                <img src='$imagePath' alt='Table Image' style='width: 50px; height: 50px; object-fit: cover; margin-right: 10px;'>
                                <div>
                                    <strong>Table #" . htmlspecialchars($row['table_number']) . "</strong> - " . $row['reservation_count'] . " Reservation
                                </div>
                              </li>";
                    }
                } else {
                    // If no reservations found, display a message
                    echo "<li class='list-group-item'>No reservations found.</li>";
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
    <script src="Js/viewmessage.js"></script>


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



</body>
</html>
