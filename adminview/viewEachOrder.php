<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="container">
    <h2>Customer Receipt Items</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>S.N.</th>
                <th>Receipt ID</th>
                <th>Order ID</th>
                <th>Item Name</th>
                <th>Item Type</th> <!-- Category Name -->
                <th>Total Price</th> <!-- Total price for combined quantities -->
                <th>Quantity</th>
                <th>Order Time</th>
                <th>Feedback</th>
            </tr>
        </thead>
        <tbody>
        <?php
            include_once "../assets/config.php"; // Ensure the correct path

            // Validate and sanitize user ID
            if (!isset($_GET['userID']) || empty($_GET['userID']) || !is_numeric($_GET['userID'])) {
                echo "<tr><td colspan='9'>Invalid or missing user ID.</td></tr>";
                exit;
            }

            $userID = intval($_GET['userID']);  // Sanitize the user ID

            // SQL query to get all receipt items for a user, with product and category details
            $sql = "
                SELECT ri.receipt_id,
                       o.order_id,
                       pi.product_name AS item_name,
                       pc.category_name AS item_type,
                       ri.item_total_price AS total_price,  -- Total price for the item
                       ri.quantity AS total_quantity,       -- Quantity of the item
                       o.order_time,
                       o.feedback
                FROM receipt_items ri
                JOIN product_items pi ON ri.product_id = pi.product_id
                JOIN product_categories pc ON pi.category_id = pc.category_id
                LEFT JOIN orders o ON ri.order_id = o.order_id
                WHERE ri.user_id = ?
                ORDER BY ri.receipt_id, o.order_time";  // Order by receipt ID and order time

            // Prepare and execute the query
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $userID);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $count = 1;
                    // Loop through each receipt item and display it
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= htmlspecialchars($row['receipt_id']) ?></td>
                            <td><?= htmlspecialchars($row['order_id']) ?></td>
                            <td><?= htmlspecialchars($row['item_name']) ?></td>
                            <td><?= htmlspecialchars($row['item_type']) ?></td> <!-- Use category_name from product_categories -->
                            <td><?= number_format($row['total_price'], 2) ?></td> <!-- Total price for the item -->
                            <td><?= htmlspecialchars($row['total_quantity']) ?></td> <!-- Quantity -->
                            <td><?= date("F j, Y, g:i a", strtotime($row["order_time"])) ?></td>
                            <td><?= htmlspecialchars($row['feedback'] ?? 'N/A') ?></td>
                        </tr>
                        <?php
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='9'>No receipt items found for this user.</td></tr>";
                }
                $stmt->close();
            } else {
                echo "<tr><td colspan='9'>Error preparing statement: " . htmlspecialchars($conn->error) . "</td></tr>";
            }
            $conn->close();
        ?>
        </tbody>
    </table>
</div>
