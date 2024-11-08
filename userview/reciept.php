<?php
session_name("user_session");
session_start(); // Start the session at the beginning of the script

include '../assets/config.php'; // Include your database configuration file

// Check if the user ID is set in the session
if (!isset($_SESSION['user_id'])) {
    echo "<p>Error: User not logged in. Please log in to view your receipts.</p>";
    exit();
}

// Get the current date for displaying today's receipts
$current_date = date('Y-m-d');
$user_id = $_SESSION['user_id']; // Get the user ID from the session

// Fetch all orders and their related reservations for the logged-in user, grouped by date
$query_orders = "
    SELECT o.*, DATE(o.order_time) as order_date, r.reservation_date, r.table_id, r.status as reservation_status, r.custom_note, t.table_number
    FROM orders o
    LEFT JOIN reservations r ON o.reservation_id = r.reservation_id
    LEFT JOIN tables t ON r.table_id = t.table_id
    WHERE o.user_id = ? AND o.status != 'Canceled'
    ORDER BY DATE(o.order_time) DESC, o.order_time DESC
";
$stmt_orders = $conn->prepare($query_orders);
$stmt_orders->bind_param('i', $user_id);
$stmt_orders->execute();
$orders_result = $stmt_orders->get_result();
$orders = $orders_result->fetch_all(MYSQLI_ASSOC);
$stmt_orders->close();

// Group orders by date
$orders_grouped_by_date = [];
foreach ($orders as $order) {
    $order_date = $order['order_date'];
    $orders_grouped_by_date[$order_date][] = $order;
}
?>

<div class="container py-4">
    <h2>Receipts</h2>
    <?php if ($orders_grouped_by_date): ?>
        <?php foreach ($orders_grouped_by_date as $order_date => $orders): ?>
            <h3 class="text-success"><?= date('l, F j, Y', strtotime($order_date)); ?></h3>
            <?php foreach ($orders as $order): ?>
                <div class="card mb-2" onclick="showOrderDetails(<?= $order['order_id']; ?>)">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0"><strong>&#x20B1;<?= htmlspecialchars(number_format($order['total_amount'], 2)); ?></strong></p>
                            <small><?= date('g:i A', strtotime($order['order_time'])); ?></small>
                        </div>
                        <div>
                            <small class="text-muted">#<?= htmlspecialchars($order['order_id']); ?></small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No receipts found.</p>
    <?php endif; ?>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title text-center w-100" id="receiptModalLabel">Payment Receipt</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <h4 class="font-weight-bold">Dine&Watch</h4>
                    <div class="receipt-divider"></div>
                </div>
                <h6 class="font-weight-bold">Order Summary</h6>
                <ul class="list-unstyled small" id="orderSummary"></ul>
                <div class="receipt-divider"></div>
                <h6 class="font-weight-bold">Reservation Summary</h6>
                <div id="reservationSummary"></div>
                <div class="receipt-divider"></div>
                <div class="text-right">
                    <h6 class="font-weight-bold">Total Payment: &#x20B1;<span id="totalPayment"></span></h6>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Cancel Button -->
                <button type="button" class="btn btn-danger btn-sm" onclick="cancelReservation()">Cancel Reservation</button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Go Back</button>
            </div>
        </div>
    </div>
</div>

<script>
function showOrderDetails(orderId) {
    const orders = <?= json_encode($orders); ?>; // Pass PHP order and reservation data to JS
    const order = orders.find(o => o.order_id == orderId);

    if (!order) {
        alert('Order details not found.');
        return;
    }

    // Populate order summary
    let orderSummary = `
        <p><strong>Order ID:</strong> #${order.order_id}</p>
        <p><strong>Total Amount:</strong> &#x20B1;${parseFloat(order.total_amount).toFixed(2)}</p>
        <p><strong>Order Time:</strong> ${new Date(order.order_time).toLocaleString()}</p>
        <p><strong>Status:</strong> ${order.status}</p>
        <p><strong>Payment Method:</strong> ${order.payment_method}</p>
    `;

    // Populate reservation summary if available
    let reservationSummary = '';
    if (order.reservation_id) {
        reservationSummary = `
            <p><strong>Reservation ID:</strong> ${order.reservation_id}</p>
            <p><strong>Reservation Date:</strong> ${order.reservation_date}</p>
            <p><strong>Table Number:</strong> ${order.table_number || 'N/A'}</p>
            <p><strong>Reservation Status:</strong> ${order.reservation_status}</p>
            <p><strong>Note:</strong> ${order.custom_note || 'N/A'}</p>
        `;
    } else {
        reservationSummary = '<p class="text-muted">No reservation associated with this order.</p>';
    }

    document.getElementById('orderSummary').innerHTML = orderSummary;
    document.getElementById('reservationSummary').innerHTML = reservationSummary;
    document.getElementById('totalPayment').innerText = parseFloat(order.total_amount).toFixed(2);

    // Show modal
    $('#receiptModal').modal('show');
}

function cancelReservation() {
    if (confirm('Are you sure you want to cancel this reservation?')) {
        alert('Reservation cancelled successfully.');
        // Add AJAX code here to handle the backend cancellation process
        $('#receiptModal').modal('hide');
    }
}
</script>
