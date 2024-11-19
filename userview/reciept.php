<?php
session_name("user_session");
session_start();

include '../assets/config.php'; // Include your database configuration file

// Check if the user ID is set in the session
if (!isset($_SESSION['user_id'])) {
    echo "<p>Error: User not logged in. Please log in to view your receipts.</p>";
    exit();
}

$current_date = date('Y-m-d');
$user_id = $_SESSION['user_id']; // Get the user ID from the session

$query_orders = "
    SELECT o.*, 
           DATE(o.order_time) AS order_date, 
           r.reservation_date, 
           r.table_id, 
           r.status AS reservation_status, 
           r.custom_note, 
           t.table_number, 
           re.receipt_id,
           re.payment_method, 
           ri.product_id, 
           ri.quantity, 
           ri.item_total_price, 
           pi.product_name, 
           pi.price AS product_price
    FROM orders o
    LEFT JOIN reservations r ON o.reservation_id = r.reservation_id
    LEFT JOIN tables t ON r.table_id = t.table_id
    LEFT JOIN receipts re ON o.order_id = re.order_id
    LEFT JOIN receipt_items ri ON re.receipt_id = ri.receipt_id
    LEFT JOIN product_items pi ON ri.product_id = pi.product_id
    WHERE o.user_id = ? AND o.status != 'Canceled'
    ORDER BY DATE(o.order_time) DESC, o.order_time DESC
";

$stmt_orders = $conn->prepare($query_orders);
$stmt_orders->bind_param('i', $user_id);
$stmt_orders->execute();
$orders_result = $stmt_orders->get_result();
$orders = $orders_result->fetch_all(MYSQLI_ASSOC);
$stmt_orders->close();

// Group orders by receipt_id and reservation_id
$orders_grouped = [];
foreach ($orders as $order) {
    $receipt_id = $order['receipt_id'];

    if (!isset($orders_grouped[$receipt_id])) {
        $orders_grouped[$receipt_id] = [
            'reservation_id' => $order['reservation_id'],
            'order_date' => $order['order_date'],
            'reservation_date' => $order['reservation_date'],
            'table_number' => $order['table_number'],
            'reservation_status' => $order['reservation_status'],
            'custom_note' => $order['custom_note'],
            'payment_method' => $order['payment_method'],
            'products' => []
        ];
    }

    $orders_grouped[$receipt_id]['products'][] = [
        'product_name' => $order['product_name'],
        'quantity' => $order['quantity'],
        'item_total_price' => $order['item_total_price'],
        'product_price' => $order['product_price'],
    ];
}
?>

<div class="container py-4">
    <h2>Receipts</h2>
    <?php if ($orders_grouped): ?>
        <?php 
        // Group orders by their date
        $orders_by_date = [];
        foreach ($orders_grouped as $receipt_id => $order) {
            $order_date = date('Y-m-d', strtotime($order['order_date'])); // Get the date in Y-m-d format
            if (!isset($orders_by_date[$order_date])) {
                $orders_by_date[$order_date] = [];
            }
            $orders_by_date[$order_date][] = ['receipt_id' => $receipt_id, 'order' => $order];
        }

        // Loop through each date and display orders
        foreach ($orders_by_date as $date => $orders): ?>
            <h3 class="text-success"><?= date('l, F j, Y', strtotime($date)); ?></h3>
            <?php foreach ($orders as $order): ?>
                <div class="card mb-2" onclick="showOrderDetails(this)" data-receipt='<?= json_encode($order['order']); ?>'>
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0"><strong>&#x20B1;<?= htmlspecialchars(number_format(array_sum(array_column($order['order']['products'], 'item_total_price')), 2)); ?></strong></p>
                            <small><?= date('g:i A', strtotime($order['order']['order_date'])); ?></small>
                        </div>
                        <div>
                            <small class="text-muted">#<?= htmlspecialchars($order['receipt_id']); ?></small>
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
                <button type="button" class="btn btn-success btn-sm" onclick="addOrder()">Add Order</button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Go Back</button>
            </div>
        </div>
    </div>
</div>

<script>
function showOrderDetails(element) {
    try {
        const order = JSON.parse(element.getAttribute('data-receipt'));

        if (!order) {
            alert('Order details not found.');
            return;
        }

        // Populate order summary
        let orderSummary = `
            <div class="d-flex justify-content-between">
                <div>
                    <p><strong>Order ID:</strong> #${order.reservation_id || 'N/A'}</p>
                    <p><strong>Total Amount:</strong> &#x20B1;${parseFloat(order.products.reduce((sum, product) => sum + (parseFloat(product.product_price) * product.quantity), 0)).toFixed(2)}</p>
                    <p><strong>Order Time:</strong> ${order.order_date ? new Date(order.order_date).toLocaleString() : 'N/A'}</p>
                    <p><strong>Status:</strong> ${order.reservation_status || 'N/A'}</p>
                    <p><strong>Payment Method:</strong> ${order.payment_method || 'N/A'}</p>
                </div>
            </div>
        `;

        // Add all product details to the order summary
        let productDetails = '';
        let totalProductPrice = 0; // Variable to track the total product price

        order.products.forEach(product => {
            const productTotal = parseFloat(product.product_price) * product.quantity; // Calculate the total price for this product
            totalProductPrice += productTotal; // Add to the total price for all products

            productDetails += `
                <div class="d-flex justify-content-between">
                    <div>
                        <strong>${product.product_name}</strong>
                    </div>
                    <div>
                        <span>Quantity: ${product.quantity}</span>
                    </div>
                    <div>
                        <span>₱${parseFloat(product.product_price).toFixed(2)} x ${product.quantity} = ₱${productTotal.toFixed(2)}</span>
                    </div>
                </div>
            `;
        });

        orderSummary += productDetails;

        document.getElementById('orderSummary').innerHTML = orderSummary;

        // Populate reservation summary
        let reservationSummary = `
            <p><strong>Reservation Date:</strong> ${order.reservation_date || 'N/A'}</p>
            <p><strong>Table Number:</strong> ${order.table_number || 'N/A'}</p>
            <p><strong>Reservation Status:</strong> ${order.reservation_status || 'N/A'}</p>
            <p><strong>Note:</strong> ${order.custom_note || 'N/A'}</p>
        `;

        document.getElementById('reservationSummary').innerHTML = reservationSummary;

        // Display the total payment (sum of price * quantity for all products)
        document.getElementById('totalPayment').innerText = totalProductPrice.toFixed(2);

        // Show modal
        $('#receiptModal').modal('show');
    } catch (error) {
        console.error("Error showing order details:", error);
        alert('Failed to load order details. Please try again.');
    }
}

</script>
