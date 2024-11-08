<?php
session_name("owner_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once "../assets/config.php"; // Ensure the correct path for your config file
?>

<div class="container-fluid"> <!-- Full-width container -->
    <div class="row justify-content-center">
        <div class="col-12">
            <div id="ordersBtn" class="text-center">
                <h2>Order Details</h2>

                <!-- Table view for large screens and above -->
                <div class="table-responsive d-none d-lg-block"> <!-- Hide on medium and smaller screens -->
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>O.N.</th>
                                <th>Customer</th>
                                <th>Contact</th>
                                <th>Order Date</th>
                                <th>Total</th>
                                <th>Order Status</th>
                                <th>Payment Method</th>
                                <th>More Details</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql = "
                            SELECT o.order_id, 
                                   CONCAT(u.first_name, ' ', u.last_name) AS customer_name, 
                                   u.contact_number, 
                                   o.order_time, 
                                   o.total_amount AS order_total,
                                   o.status AS order_status, 
                                   o.payment_method
                            FROM orders o
                            LEFT JOIN users u ON o.user_id = u.user_id
                            GROUP BY o.order_id
                        ";

                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $buttonClass = match($row["order_status"]) {
                                    'Pending' => 'btn-danger',
                                    'In-Progress' => 'btn-warning',
                                    'Completed' => 'btn-success',
                                    'Canceled' => 'btn-secondary',
                                    default => 'btn-secondary',
                                };
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row["order_id"]) ?></td>
                                    <td><?= htmlspecialchars($row["customer_name"]) ?></td>
                                    <td><?= isset($row["contact_number"]) ? htmlspecialchars($row["contact_number"]) : "N/A" ?></td>
                                    <td><?= date("F j, Y", strtotime($row["order_time"])) ?></td>
                                    <td>&#8369;<?= number_format($row["order_total"], 2) ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn <?= $buttonClass ?> dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <?= htmlspecialchars($row["order_status"]) ?>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="javascript:void(0);" onclick="ChangeOrderStatus('<?= htmlspecialchars($row['order_id']) ?>', 'Pending')">Pending</a>
                                                <a class="dropdown-item" href="javascript:void(0);" onclick="ChangeOrderStatus('<?= htmlspecialchars($row['order_id']) ?>', 'In-Progress')">In-Progress</a>
                                                <a class="dropdown-item" href="javascript:void(0);" onclick="ChangeOrderStatus('<?= htmlspecialchars($row['order_id']) ?>', 'Completed')">Completed</a>
                                                <a class="dropdown-item" href="javascript:void(0);" onclick="ChangeOrderStatus('<?= htmlspecialchars($row['order_id']) ?>', 'Canceled')">Canceled</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($row["payment_method"]) ?></td>
                                    <td>
                                        <a class="btn btn-primary openPopup" data-href="../Ownerview/viewEachOrder.php?orderID=<?= htmlspecialchars($row['order_id']) ?>" href="javascript:void(0);">View Details</a>
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
                </div> <!-- End of table-responsive -->

                <!-- Card view for medium screens and below -->
                <div class="d-lg-none"> <!-- Show only on medium and smaller screens -->
                    <div class="row">
                        <?php
                        if ($result && $result->num_rows > 0) {
                            // Reset result pointer
                            $result->data_seek(0);
                            while ($row = $result->fetch_assoc()) {
                                $buttonClass = match($row["order_status"]) {
                                    'Pending' => 'btn-danger',
                                    'In-Progress' => 'btn-warning',
                                    'Completed' => 'btn-success',
                                    'Canceled' => 'btn-secondary',
                                    default => 'btn-secondary',
                                };
                                ?>
                                <div class="col-12 mb-4"> <!-- Full width on smaller screens -->
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">Order #<?= htmlspecialchars($row["order_id"]) ?></h5>
                                            <p><strong>Customer:</strong> <?= htmlspecialchars($row["customer_name"]) ?></p>
                                            <p><strong>Contact:</strong> <?= isset($row["contact_number"]) ? htmlspecialchars($row["contact_number"]) : "N/A" ?></p>
                                            <p><strong>Order Date:</strong> <?= date("F j, Y", strtotime($row["order_time"])) ?></p>
                                            <p><strong>Total:</strong> &#8369;<?= number_format($row["order_total"], 2) ?></p>
                                            <p><strong>Status:</strong> 
                                                <button class="btn <?= $buttonClass ?>"><?= htmlspecialchars($row["order_status"]) ?></button>
                                            </p>
                                            <p><strong>Payment Method:</strong> <?= htmlspecialchars($row["payment_method"]) ?></p>
                                            <a class="btn btn-primary openPopup" data-href="../Ownerview/viewEachOrder.php?orderID=<?= htmlspecialchars($row['order_id']) ?>" href="javascript:void(0);">View Details</a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<div class='col-12'><p>No orders found.</p></div>";
                        }
                        ?>
                    </div> <!-- End of row for cards -->
                </div> <!-- End of card view -->

            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalLabel">Order Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="order-view-modal modal-body"></div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.openPopup').on('click', function() {
            var dataURL = $(this).attr('data-href');
            $('.order-view-modal').load(dataURL, function() {
                $('#viewModal').modal({ show: true });
            });
        });
    });
</script>
