<?php
session_name("owner_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once "../assets/config.php";

$orderID = intval($_GET['orderID'] ?? 0);
if ($orderID <= 0) {
    echo "<p>Invalid Order ID.</p>";
    exit;
}

// Fetch order details
$order_sql = "
    SELECT o.order_id, 
           CONCAT(u.first_name, ' ', u.last_name) AS customer_name, 
           u.contact_number, 
           o.order_time, 
           o.total_amount AS order_total,
           o.status AS order_status,
           o.payment_method
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    WHERE o.order_id = ?
";

$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("i", $orderID);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$order_data = $order_result->fetch_assoc();
$order_stmt->close();

if (!$order_data) {
    echo "<p>Order not found.</p>";
    exit;
}

// Determine button class based on status
$buttonClass = '';
switch ($order_data['order_status']) {
    case 'Pending':
        $buttonClass = 'btn-warning';
        break;
    case 'In-Progress':
        $buttonClass = 'btn-info';
        break;
        case 'paid in advance':
            $buttonClass = 'btn-warning';
            break;
    case 'Completed':
        $buttonClass = 'btn-success';
        break;
    case 'Canceled':
        $buttonClass = 'btn-danger';
        break;
}
?>

<div class="container">
    <h3>Order Receipt Details</h3>
    <p><strong>Order #<?= htmlspecialchars($order_data['order_id']) ?></strong></p>
    <p><strong>Customer:</strong> <?= htmlspecialchars($order_data['customer_name']) ?></p>
    <p><strong>Contact:</strong> <?= htmlspecialchars($order_data['contact_number']) ?></p>
    <p><strong>Order Date:</strong> <?= date("F j, Y, g:i a", strtotime($order_data['order_time'])) ?></p>
    <p><strong>Total Amount:</strong> &#8369;<?= number_format($order_data['order_total'], 2) ?></p>
    <p><strong>Payment Method:</strong> <?= htmlspecialchars($order_data['payment_method']) ?></p>
    
    <!-- Order Status Action Button -->
    <div class="mb-3">
        <label><strong>Order Status:</strong></label>
        <div class="dropdown">
            <button class="btn <?= $buttonClass ?> dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= htmlspecialchars($order_data['order_status']) ?>
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="javascript:void(0);" onclick="ChangeOrderStatus(<?= $orderID ?>, 'Pending')">Pending</a>
                <a class="dropdown-item" href="javascript:void(0);" onclick="ChangeOrderStatus(<?= $orderID ?>, 'In-Progress')">In-Progress</a>
                <a class="dropdown-item" href="javascript:void(0);" onclick="ChangeOrderStatus(<?= $orderID ?>, 'paid in advance')">Paid in advance</a>
                <a class="dropdown-item" href="javascript:void(0);" onclick="ChangeOrderStatus(<?= $orderID ?>, 'Completed')">Completed</a>
                <a class="dropdown-item" href="javascript:void(0);" onclick="ChangeOrderStatus(<?= $orderID ?>, 'Canceled')">Canceled</a>
            </div>
        </div>
    </div>
</div>

<script>

</script>
