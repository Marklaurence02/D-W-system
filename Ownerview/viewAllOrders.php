<?php
session_name("owner_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once "../assets/config.php"; // Ensure the correct path for your config file
?>

<div class="container-fluid p-3">
        <div class="col-lg-10 col-md-12">
            <div id="ordersBtn" class="text-center mb-3">
                <h2>Order Details</h2>
            </div>

            <!-- Responsive table with DataTables -->
            <div class="table-responsive">
                <table id="ordersTable" class="table table-striped table-hover table-bordered display nowrap">
                    <thead class="thead-dark">
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
                        $sql = "SELECT o.order_id, CONCAT(u.first_name, ' ', u.last_name) AS customer_name, 
                                       u.contact_number, o.order_time, o.total_amount AS order_total, 
                                       o.status AS order_status, o.payment_method 
                                FROM orders o
                                LEFT JOIN users u ON o.user_id = u.user_id
                                GROUP BY o.order_id";
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
                                        <button class="btn <?= $buttonClass ?>"><?= htmlspecialchars($row["order_status"]) ?></button>
                                    </td>
                                    <td><?= htmlspecialchars($row["payment_method"]) ?></td>
                                    <td>
                                        <a class="btn btn-primary openPopup" 
                                           data-href="../Ownerview/viewEachOrder.php?orderID=<?= htmlspecialchars($row['order_id']) ?>" 
                                           href="javascript:void(0);">View Details</a>
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


function filterCards() {
    // Get the search input value
    const searchValue = document.getElementById('cardSearchInput').value.toLowerCase();

    // Get all the card items
    const cards = document.querySelectorAll('.card-item');

    // Loop through the cards and show/hide based on search input
    cards.forEach((card) => {
        const cardContent = card.textContent.toLowerCase(); // Get all text content in the card
        card.style.display = cardContent.includes(searchValue) ? 'block' : 'none'; // Show if matches, else hide
    });
}

$(document).ready(function() {
    $('#ordersTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Order Report'
            },
            {
                extend: 'csvHtml5',
                title: 'Order Report'
            },
            {
                extend: 'pdfHtml5',
                title: 'Order Report',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                }
            },
            {
                extend: 'print',
                title: 'Order Report',
                autoPrint: true
            }
        ],
        scrollX: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        order: [[3, 'desc']]
    });

    // Handle popup modal for viewing order details
    $('.openPopup').on('click', function() {
        var dataURL = $(this).attr('data-href');
        $('.order-view-modal').load(dataURL, function() {
            $('#viewModal').modal({ show: true });
        });
    });
});


</script>
