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
?>

<div class="container">
    <h3>Order Receipt Details</h3>
    <p><strong>Order #<?= htmlspecialchars($order_data['order_id']) ?></strong></p>
    
    <!-- Table layout for larger screens -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Receipt ID</th>
                    <th>Receipt Date</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    <th>Item Name</th>
                    <th>Item Type</th>
                    <th>Quantity</th>
                    <th>Item Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch receipts and their items for the specific order
                $receipts_sql = "
                    SELECT r.receipt_id, r.receipt_date, r.total_amount, r.payment_method, 
                           ri.quantity, ri.item_total_price, pi.product_name, pc.category_name AS item_type
                    FROM receipts r
                    LEFT JOIN receipt_items ri ON r.receipt_id = ri.receipt_id
                    LEFT JOIN product_items pi ON ri.product_id = pi.product_id
                    LEFT JOIN product_categories pc ON pi.category_id = pc.category_id
                    WHERE r.order_id = ?
                    ORDER BY r.receipt_date ASC
                ";

                $receipts_stmt = $conn->prepare($receipts_sql);
                $receipts_stmt->bind_param("i", $orderID);
                $receipts_stmt->execute();
                $receipts_result = $receipts_stmt->get_result();
                $cumulative_total = 0;

                while ($row = $receipts_result->fetch_assoc()) {
                    $cumulative_total = $row['total_amount'];
                    echo "<tr>
                            <td>" . htmlspecialchars($row['receipt_id']) . "</td>
                            <td>" . date("F j, Y, g:i a", strtotime($row['receipt_date'])) . "</td>
                            <td>&#8369;" . number_format($row['total_amount'], 2) . "</td>
                            <td>" . htmlspecialchars($row['payment_method']) . "</td>
                            <td>" . htmlspecialchars($row['product_name']) . "</td>
                            <td>" . htmlspecialchars($row['item_type']) . "</td>
                            <td>" . htmlspecialchars($row['quantity']) . "</td>
                            <td>&#8369;" . number_format($row['item_total_price'], 2) . "</td>
                        </tr>";
                }
                $receipts_stmt->close();
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" style="text-align: right;"><strong>Cumulative Total:</strong></td>
                    <td><strong>&#8369;<?= number_format($cumulative_total, 2) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <!-- Card layout for smaller screens -->
    <div class="d-block d-lg-none">
        <?php
        $receipts_stmt = $conn->prepare($receipts_sql);
        $receipts_stmt->bind_param("i", $orderID);
        $receipts_stmt->execute();
        $receipts_result = $receipts_stmt->get_result();
        
        while ($row = $receipts_result->fetch_assoc()) {
            echo "
            <div class='card mb-3'>
                <div class='card-body'>
                    <h5 class='card-title'>Receipt ID: " . htmlspecialchars($row['receipt_id']) . "</h5>
                    <p><strong>Date:</strong> " . date("F j, Y, g:i a", strtotime($row['receipt_date'])) . "</p>
                    <p><strong>Total Amount:</strong> &#8369;" . number_format($row['total_amount'], 2) . "</p>
                    <p><strong>Payment Method:</strong> " . htmlspecialchars($row['payment_method']) . "</p>
                    <p><strong>Item:</strong> " . htmlspecialchars($row['product_name']) . "</p>
                    <p><strong>Type:</strong> " . htmlspecialchars($row['item_type']) . "</p>
                    <p><strong>Quantity:</strong> " . htmlspecialchars($row['quantity']) . "</p>
                    <p><strong>Item Total Price:</strong> &#8369;" . number_format($row['item_total_price'], 2) . "</p>
                </div>
            </div>";
        }
        $receipts_stmt->close();
        ?>
        <div class="card">
            <div class="card-body">
                <p><strong>Cumulative Total:</strong> &#8369;<?= number_format($cumulative_total, 2) ?></p>
            </div>
        </div>
    </div>
</div>

<?php $conn->close(); ?>
