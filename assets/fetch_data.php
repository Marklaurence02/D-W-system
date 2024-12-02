<?php
include 'config.php';

// Initialize variables with default values
$totalSales = 0;
$totalOrders = 0;
$totalSold = 0;
$totalCustomers = 0;

// Function to execute a query and return the result or log an error
function fetchSingleValue($conn, $query, &$variable, $label) {
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $variable = $row ? ($row[$label] ?? 0) : 0;
    } else {
        error_log("Error fetching $label: " . $conn->error);
        return false;
    }
    return true;
}

// Fetch total sales using receipt_items table (Paid in advance and completed)
if (!fetchSingleValue($conn, "
    SELECT SUM(receipt_items.item_total_price) AS totalSales 
    FROM receipt_items
    JOIN orders ON receipt_items.receipt_id = orders.order_id 
    WHERE orders.status IN ('paid in advance', 'completed','In-Progress','Pending')
", $totalSales, 'totalSales')) {
    echo json_encode(['error' => 'Failed to fetch total sales']);
    $conn->close();
    exit;
}

// Fetch total orders (excluding canceled orders)
if (!fetchSingleValue($conn, "
    SELECT COUNT(order_id) AS totalOrders 
    FROM orders 
    WHERE status != 'Canceled'
", $totalOrders, 'totalOrders')) {
    echo json_encode(['error' => 'Failed to fetch total orders']);
    $conn->close();
    exit;
}

// Fetch total items sold (only completed or paid in advance)
if (!fetchSingleValue($conn, "
    SELECT SUM(receipt_items.quantity) AS totalSold 
    FROM receipt_items 
    JOIN orders ON receipt_items.receipt_id = orders.order_id 
    WHERE orders.status IN ('paid in advance', 'completed','In-Progress','Pending')
", $totalSold, 'totalSold')) {
    echo json_encode(['error' => 'Failed to fetch total sold']);
    $conn->close();
    exit;
}

// Fetch total customers
if (!fetchSingleValue($conn, "
    SELECT COUNT(DISTINCT user_id) AS totalCustomers 
    FROM orders
", $totalCustomers, 'totalCustomers')) {
    echo json_encode(['error' => 'Failed to fetch total customers']);
    $conn->close();
    exit;
}

// Fetch order distribution data (status counts)
$orderStatuses = [];
$orderCounts = [];
$sql = "
    SELECT status, COUNT(*) AS count 
    FROM orders 
    GROUP BY status
";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orderStatuses[] = $row['status'];
        $orderCounts[] = $row['count'];
    }
} else {
    error_log("Error fetching order distribution data: " . $conn->error);
}

// Fetch monthly sales data for chart (All Statuses)
$chartLabels = [];
$chartData = [];
$sql = "
    SELECT DATE_FORMAT(order_time, '%Y-%m-%d') AS date, SUM(receipt_items.item_total_price) AS total_sales 
    FROM receipt_items 
    JOIN orders ON receipt_items.receipt_id = orders.order_id
    GROUP BY date 
    ORDER BY date
";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $chartLabels[] = $row['date'];
        $chartData[] = $row['total_sales'];
    }
} else {
    error_log("Error fetching chart data: " . $conn->error);
}

$conn->close();

// Return data as JSON
echo json_encode([
    'totalSales' => $totalSales,
    'totalOrders' => $totalOrders,
    'totalSold' => $totalSold,
    'totalCustomers' => $totalCustomers,
    'orderStatuses' => $orderStatuses,
    'orderCounts' => $orderCounts,
    'chartLabels' => $chartLabels,
    'chartData' => $chartData
]);
?>
