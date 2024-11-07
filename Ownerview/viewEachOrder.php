<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once "../assets/config.php"; // Adjust the path to your config file

// Validate and sanitize `orderID`
if (!isset($_GET['orderID']) || empty($_GET['orderID']) || !is_numeric($_GET['orderID'])) {
    echo "<tr><td colspan='8'>Invalid or missing order ID.</td></tr>";
    exit;
}

$orderID = intval($_GET['orderID']); // Sanitize `orderID`
?>

<div class="container-fluid">
    <h2 class="text-center">Order Receipt and Item Details</h2>

    <!-- Table view for large screens and above -->
    <div class="table-responsive d-none d-lg-block">
        <table class="table table-striped table-bordered">
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
                // Combined query to fetch receipts and their items for the specified `orderID`
                $sql = "
                    SELECT r.receipt_id, 
                           r.receipt_date, 
                           r.total_amount, 
                           r.payment_method, 
                           pi.product_name AS item_name, 
                           pc.category_name AS item_type, 
                           ri.quantity, 
                           ri.item_total_price
                    FROM receipts r
                    JOIN receipt_items ri ON r.receipt_id = ri.receipt_id
                    JOIN product_items pi ON ri.product_id = pi.product_id
                    JOIN product_categories pc ON pi.category_id = pc.category_id
                    WHERE r.order_id = ?
                    ORDER BY r.receipt_id, r.receipt_date
                ";
                
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("i", $orderID);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['receipt_id']) ?></td>
                                <td><?= date("F j, Y, g:i a", strtotime($row['receipt_date'])) ?></td>
                                <td>&#8369;<?= number_format($row['total_amount'], 2) ?></td>
                                <td><?= htmlspecialchars($row['payment_method']) ?></td>
                                <td><?= htmlspecialchars($row['item_name']) ?></td>
                                <td><?= htmlspecialchars($row['item_type']) ?></td>
                                <td><?= htmlspecialchars($row['quantity']) ?></td>
                                <td>&#8369;<?= number_format($row['item_total_price'], 2) ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='8'>No receipts or items found for this order.</td></tr>";
                    }
                    $stmt->close();
                } else {
                    echo "<tr><td colspan='8'>Error preparing statement: " . htmlspecialchars($conn->error) . "</td></tr>";
                }
            ?>
            </tbody>
        </table>
    </div>

    <!-- Card view for medium screens and below -->
    <div class="d-lg-none">
        <div class="row">
            <?php
            if ($result && $result->num_rows > 0) {
                // Reset result pointer
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="col-12 mb-4"> <!-- Full width on smaller screens -->
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Receipt #<?= htmlspecialchars($row["receipt_id"]) ?></h5>
                                <p><strong>Receipt Date:</strong> <?= date("F j, Y, g:i a", strtotime($row["receipt_date"])) ?></p>
                                <p><strong>Total Amount:</strong> &#8369;<?= number_format($row["total_amount"], 2) ?></p>
                                <p><strong>Payment Method:</strong> <?= htmlspecialchars($row["payment_method"]) ?></p>
                                <p><strong>Item Name:</strong> <?= htmlspecialchars($row["item_name"]) ?></p>
                                <p><strong>Item Type:</strong> <?= htmlspecialchars($row["item_type"]) ?></p>
                                <p><strong>Quantity:</strong> <?= htmlspecialchars($row["quantity"]) ?></p>
                                <p><strong>Item Total Price:</strong> &#8369;<?= number_format($row["item_total_price"], 2) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<div class='col-12'><p>No receipts or items found for this order.</p></div>";
            }
            ?>
        </div> <!-- End of row for cards -->
    </div> <!-- End of card view -->
</div>

<?php
$conn->close();
