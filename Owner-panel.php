<?php

include_once "assets/config.php"; // Database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/panel.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
    <script src="Js/sales_chart.js"></script> <!-- Include your custom chart JS file -->
</head>
<body>
    <?php
        include "Header_nav/ownerHeader.php"; // Header for admin panel
        include "Header_nav/own-sidebar.php"; // Sidebar for navigation
    ?>


    <div id="main-content" class="container allContent-section py-4">
        <div class="row">
            <!-- Total Sales -->
            <div class="col-sm-3">
                <div class="card text-center bg-success text-white">
                    <i class="fa fa-dollar mb-2" style="font-size: 70px;"></i>
                    <h4>Total Sales</h4>
                    <h5 id="totalSales">
                    <?php
                        // Calculate total sales
                        $sql = "SELECT SUM(total_amount) as total_sales FROM orders";
                        $result = $conn->query($sql);
                        $row = $result->fetch_assoc();
                        echo number_format($row['total_sales'] ?? 0, 2); // Default to 0 if null
                    ?>
                    </h5>
                </div>
            </div>

       


        </div>

 
    
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
    <script src="Js/Ownerpanel.js"></script>
    <script src="Js/navb.js"></script>


    
</body>
</html>

<script>
    // Fetch data and dynamically update the stats
fetch('assets/fetch_data.php')
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        const totalCustomers = data.totalCustomers ?? 0; // Default to 0 if null

        // Update the Total Customers dynamically
        document.getElementById('totalCustomers').innerText = totalCustomers;
    })
    .catch(error => console.error('Error fetching customer data:', error));

</script>