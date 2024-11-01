<div class="container">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>S.N.</th>
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

            // Validate and sanitize order ID
            if (!isset($_GET['orderID']) || empty($_GET['orderID']) || !is_numeric($_GET['orderID'])) {
                echo "<tr><td colspan='8'>Invalid or missing order ID.</td></tr>";
                exit;
            }

            $ID = intval($_GET['orderID']);  // Sanitize the order ID

            // SQL query to get order details with product and category information
            $sql = "
                SELECT o.order_id, 
                       pi.product_name AS item_name, 
                       pc.category_name AS item_type,  -- Fetch category_name from product_categories
                       SUM(pi.price * oi.quantity) AS total_price,  -- Calculate total price from product_items.price
                       SUM(oi.quantity) AS total_quantity,  -- Sum the quantity
                       o.order_time, 
                       o.feedback
                FROM orders o
                JOIN order_items oi ON o.order_id = oi.order_id
                JOIN product_items pi ON oi.product_id = pi.product_id
                JOIN product_categories pc ON pi.category_id = pc.category_id  -- Join for category_name
                WHERE o.order_id = ?
                GROUP BY oi.product_id";  // Group by product to combine quantities

            // Prepare and execute the query
            if ($stmt = $conn->prepare($sql)) {
                // Bind the order ID to the query
                $stmt->bind_param("i", $ID);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $count = 1;
                    // Loop through each order item and display it
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= htmlspecialchars($row['order_id']) ?></td>
                            <td><?= htmlspecialchars($row['item_name']) ?></td>
                            <td><?= htmlspecialchars($row['item_type']) ?></td> <!-- Use category_name from product_categories -->
                            <td><?= number_format($row['total_price'], 2) ?></td> <!-- Total price for combined quantity -->
                            <td><?= htmlspecialchars($row['total_quantity']) ?></td> <!-- Combined quantity -->
                            <td><?= date("F j, Y, g:i a", strtotime($row["order_time"])) ?></td>
                            <td><?= htmlspecialchars($row['feedback'] ?? 'N/A') ?></td>
                        </tr>
                        <?php
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='8'>No items found for this order.</td></tr>";
                }
                // Close the statement
                $stmt->close();
            } else {
                echo "<tr><td colspan='8'>Error preparing statement: " . htmlspecialchars($conn->error) . "</td></tr>";
            }

            // Close the connection
            $conn->close();
        ?>
        </tbody>
    </table>
</div>
