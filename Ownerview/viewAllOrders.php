<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10"> <!-- Adjust the column size as needed -->
            <div id="ordersBtn" class="text-center">
                <h2>Order Details</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>O.N.</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Order Date</th>
                            <th>Total</th> <!-- New Total column -->
                            <th>Order Status</th>
                            <th>Payment Method</th> <!-- Payment Method column -->
                            <th>More Details</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    include_once "../assets/config.php"; // Ensure connection to the database

                    // Query to fetch orders, customer details, and calculate total amount
                    $sql = "
                        SELECT o.order_id, 
                               CONCAT(u.first_name, ' ', u.last_name) AS customer_name, 
                               u.contact_number, 
                               o.order_time, 
                               SUM(oi.totalprice * oi.quantity) AS total_amount,  /* Calculating the total amount from order_items */
                               o.status AS order_status, 
                               o.payment_method  /* Fetch payment method directly from orders table */
                        FROM orders o
                        LEFT JOIN users u ON o.user_id = u.user_id
                        LEFT JOIN order_items oi ON o.order_id = oi.order_id  /* Joining with order_items to calculate total amount */
                        GROUP BY o.order_id";  // Group by order_id to get one result per order

                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Set button class based on order status
                            $buttonClass = '';
                            switch ($row["order_status"]) {
                                case 'Pending':
                                    $buttonClass = 'btn-danger';
                                    break;
                                case 'In-Progress':
                                    $buttonClass = 'btn-warning';
                                    break;
                                case 'Completed':
                                    $buttonClass = 'btn-success';
                                    break;
                                case 'Canceled':
                                    $buttonClass = 'btn-secondary';
                                    break;
                                default:
                                    $buttonClass = 'btn-secondary';
                                    break;
                            }
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row["order_id"]) ?></td>
                                <td><?= htmlspecialchars($row["customer_name"]) ?></td>
                                <td><?= isset($row["contact_number"]) ? htmlspecialchars($row["contact_number"]) : "N/A" ?></td>
                                <td><?= date("F j, Y", strtotime($row["order_time"])) ?></td>
                                <td><?= number_format($row["total_amount"], 2) ?></td> <!-- Displaying the total amount -->
                                <td>
                                    <!-- Dropdown button for order statuses with dynamic color -->
                                    <div class="dropdown">
                                        <button class="btn <?= $buttonClass ?> dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <?= htmlspecialchars($row["order_status"]) ?>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="ChangeOrderStatus('<?= htmlspecialchars($row['order_id']) ?>', 'Pending')">Pending</a>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="ChangeOrderStatus('<?= htmlspecialchars($row['order_id']) ?>', 'In-Progress')">In-Progress</a>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="ChangeOrderStatus('<?= htmlspecialchars($row['order_id']) ?>', 'Completed')">Completed</a>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="ChangeOrderStatus('<?= htmlspecialchars($row['order_id']) ?>', 'Canceled')">Canceled</a>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($row["payment_method"]) ?></td> <!-- Display Payment Method -->
                                <td>
                                    <a class="btn btn-primary openPopup" data-href="../Ownerview/viewEachOrder.php?orderID=<?= htmlspecialchars($row['order_id']) ?>" href="javascript:void(0);">View</a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='8'>No orders found.</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="viewModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title">Order Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="order-view-modal modal-body"></div>
        </div><!--/ Modal content-->
    </div><!-- /Modal dialog-->
</div>

<script>
    // For view order modal  
    $(document).ready(function() {
        $('.openPopup').on('click', function() {
            var dataURL = $(this).attr('data-href');
            $('.order-view-modal').load(dataURL, function() {
                $('#viewModal').modal({ show: true });
            });
        });
    });
</script>
