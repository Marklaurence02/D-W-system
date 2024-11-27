<?php
session_start();

include_once "../assets/config.php"; // Ensure the correct path for your config file
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<div class="container-fluid p-3">
    <div class="col-12">
        <div id="ordersBtn" class="text-center mb-3">
            <h2>Order Details</h2>
        </div>

        <!-- Add this HTML above your table -->
        <div class="filter-container mb-4">
            <div class="status-filters">
                <button class="filter-btn active" data-status="all">All Orders</button>
                <button class="filter-btn" data-status="Pending">Pending</button>
                <button class="filter-btn" data-status="In-Progress">In Progress</button>
                <button class="filter-btn" data-status="Completed">Completed</button>
                <button class="filter-btn" data-status="Canceled">Canceled</button>
            </div>
        </div>

        <!-- Responsive table with DataTables -->
        <div class="table-responsive">
            <table id="ordersTable" class="table table-striped table-hover table-bordered display nowrap">
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
                                <td class="text-center">
                                    <?php
                                    $statusInfo = match($row["order_status"]) {
                                        'Pending' => [
                                            'class' => 'status-pending',
                                            'icon' => 'fa-clock',
                                            'text' => 'Pending'
                                        ],
                                        'In-Progress' => [
                                            'class' => 'status-progress',
                                            'icon' => 'fa-spinner',
                                            'text' => 'In Progress'
                                        ],
                                        'Completed' => [
                                            'class' => 'status-completed',
                                            'icon' => 'fa-check-circle',
                                            'text' => 'Completed'
                                        ],
                                        'Canceled' => [
                                            'class' => 'status-canceled',
                                            'icon' => 'fa-times-circle',
                                            'text' => 'Canceled'
                                        ],
                                        default => [
                                            'class' => 'status-default',
                                            'icon' => 'fa-question-circle',
                                            'text' => $row["order_status"]
                                        ]
                                    };
                                    ?>
                                    <span class="status-badge <?= $statusInfo['class'] ?>">
                                        <i class="fas <?= $statusInfo['icon'] ?>"></i>
                                        <?= $statusInfo['text'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $paymentIcon = match($row["payment_method"]) {
                                        'Cash' => 'fa-money-bill-wave',
                                        'GCash' => 'fa-mobile-alt',
                                        default => 'fa-credit-card'
                                    };
                                    $paymentClass = strtolower($row["payment_method"]);
                                    ?>
                                    <span class="payment-badge <?= $paymentClass ?>">
                                        <i class="fas <?= $paymentIcon ?>"></i>
                                        <?= htmlspecialchars($row["payment_method"]) ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-view-details openPopup" 
                                            data-href="../Ownerview/viewEachOrder.php?orderID=<?= htmlspecialchars($row['order_id']) ?>">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
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
            <div class="order-view-modal modal-body">
                <!-- Content will be loaded here dynamically -->
            </div>
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
    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable('#ordersTable')) {
        $('#ordersTable').DataTable().destroy();
    }
    
    // Initialize DataTable
    var table = $('#ordersTable').DataTable({
        responsive: true,
        autoWidth: false,
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rtip',
        language: {
            search: "_INPUT_",           // Removes the 'Search' label
            searchPlaceholder: "Search orders..."  // Adds placeholder
        },
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success',
                titleAttr: 'Export to Excel',
                title: 'Order Report'
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-info',
                titleAttr: 'Export to CSV',
                title: 'Order Report'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger',
                titleAttr: 'Export to PDF',
                title: 'Order Report',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                },
                customize: function (doc) {
                    // Add a header
                    doc['header'] = (function() {
                        return {
                            columns: [
                                {
                                    alignment: 'left',
                                    text: 'Company Name',
                                    fontSize: 12,
                                    margin: [10, 0]
                                },
                                {
                                    alignment: 'right',
                                    text: 'Order Report',
                                    fontSize: 12,
                                    margin: [0, 0, 10, 0]
                                }
                            ],
                            margin: [0, 0, 0, 10]
                        }
                    });

                    // Add a footer
                    doc['footer'] = (function(page, pages) {
                        return {
                            columns: [
                                {
                                    alignment: 'left',
                                    text: ['Page ', { text: page.toString() },  ' of ', { text: pages.toString() }],
                                    margin: [10, 0]
                                },
                                {
                                    alignment: 'right',
                                    text: 'Generated on: ' + new Date().toLocaleDateString(),
                                    margin: [0, 0, 10, 0]
                                }
                            ],
                            margin: [0, 0, 0, 10]
                        }
                    });

                    // Customize the layout
                    doc.content[1].table.widths = ['10%', '20%', '15%', '15%', '10%', '15%', '15%'];
                    doc.styles.tableHeader.fontSize = 10;
                    doc.styles.tableBodyEven.fontSize = 9;
                    doc.styles.tableBodyOdd.fontSize = 9;
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-primary',
                titleAttr: 'Print Table',
                title: 'Order Report',
                autoPrint: true
            }
        ]
    });
    
    // Filter button click handler
    $('.filter-btn').on('click', function() {
        var status = $(this).data('status');
        
        // Remove active class from all buttons and add to clicked button
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        // Apply the filter
        if (status === 'all') {
            table.column(5).search('').draw(); // Clear filter
        } else {
            table.column(5).search(status).draw(); // Filter by status
        }
    });

    // Initial filter (if needed)
    $('.filter-btn.active').trigger('click');

    // Custom filtering function
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var selectedStatus = $('.filter-btn.active').data('status');
        var status = data[5]; // Assuming status is in the 6th column (index 5)
        
        // If "All Orders" is selected or status matches selected filter
        if (selectedStatus === 'all' || status.includes(selectedStatus)) {
            return true;
        }
        return false;
    });

    // Status filter click handler
    $('.filter-btn').on('click', function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        // Update counter badges
        updateStatusCounts();
        
        // Redraw the table to apply filters
        table.draw();
    });

    // Function to update status counts
    function updateStatusCounts() {
        var statusCounts = {
            'all': 0,
            'Pending': 0,
            'In-Progress': 0,
            'Completed': 0,
            'Canceled': 0
        };

        // Count rows for each status
        table.rows().every(function() {
            var data = this.data();
            var status = data[5]; // Status column index
            statusCounts['all']++;
            
            // Remove any HTML tags and trim whitespace
            status = status.replace(/<[^>]*>/g, '').trim();
            
            if (statusCounts.hasOwnProperty(status)) {
                statusCounts[status]++;
            }
        });

        // Update the filter buttons without counts
        $('.filter-btn').each(function() {
            var status = $(this).data('status');
            $(this).html(status); // Remove the count from the button text
        });
    }

    // Initial count update
    updateStatusCounts();

    // View Details Modal Handler
    $(document).ready(function() {
        // Handle popup modal for viewing order details
        $('.openPopup').on('click', function() {
            var dataURL = $(this).attr('data-href');
            $('.order-view-modal').load(dataURL, function() {
                $('#viewModal').modal({ show: true });
            });
        });
    });

    // Close modal handler
    $(document).on('click', '[data-dismiss="modal"]', function() {
        $('#viewModal').modal('hide');
    });
});

    // Handle popup modal for viewing order details
    $('.openPopup').on('click', function() {
        var dataURL = $(this).attr('data-href');
        $('.order-view-modal').load(dataURL, function() {
            $('#viewModal').modal({ show: true });
        });
    });
</script>

<style>
/* DataTable Container */
.dataTables_wrapper {
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

/* Search Box Styling */
.dataTables_filter {
    position: relative;
    margin-bottom: 20px;
}

.dataTables_filter label {
    display: flex;
    align-items: center;
    margin: 0;
    width: 100%;
    position: relative;
}

.dataTables_filter input {
    width: 300px !important;
    height: 40px;
    padding: 8px 16px 8px 40px !important;
    border: 1px solid #e0e0e0;
    border-radius: 20px;
    font-size: 14px;
    transition: all 0.3s ease;
    margin: 0 !important;
}

/* Hide the "Search:" text */
.dataTables_filter label > span,
.dataTables_filter label::before {
    display: none;
}

/* Add search icon */
.dataTables_filter::before {
    content: "\f002";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    z-index: 1;
    pointer-events: none;
}

/* Focus state */
.dataTables_filter input:focus {
    outline: none;
    border-color: #FD6610;
    box-shadow: 0 0 0 3px rgba(253, 102, 16, 0.1);
}

/* Responsive */
@media (max-width: 768px) {
    .dataTables_filter input {
        width: 200px !important;
    }
}

/* Length (Entries per page) Styling */
.dataTables_length {
    float: left;
    margin-bottom: 20px;
}

.dataTables_length select {
    padding: 8px 30px 8px 15px;
    border: 2px solid #eee;
    border-radius: 20px;
    font-size: 14px;
    appearance: none;
    background: url("data:image/svg+xml,<svg height='10px' width='10px' viewBox='0 0 16 16' fill='%23000000' xmlns='http://www.w3.org/2000/svg'><path d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/></svg>") no-repeat;
    background-position: calc(100% - 12px) center;
    background-color: white;
}

.dataTables_length select:focus {
    border-color: #FD6610;
    outline: none;
}

/* Pagination Styling */
.dataTables_paginate {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    text-align: center;
}

.paginate_button {
    display: inline-block;
    min-width: 40px;
    height: 40px;
    line-height: 40px;
    margin: 0 5px;
    text-align: center;
    border-radius: 50%;
    color: #666;
    font-weight: 500;
    transition: all 0.3s ease;
    cursor: pointer;
}

.paginate_button:hover {
    background-color: #f8f9fa;
    color: #FD6610;
}

.paginate_button.current {
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%);
    color: white;
    border: none;
}

.paginate_button.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Info Text Styling */
.dataTables_info {
    margin-top: 20px;
    color: #666;
    font-size: 14px;
}

/* Table Styling */
#ordersTable {
    width: 100%;
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
}

#ordersTable th {
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%);
    color: white;
    padding: 15px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 14px;
    border: none;
}

#ordersTable td {
    padding: 15px;
    vertical-align: middle;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

#ordersTable tbody tr:hover {
    background-color: #f8f9fa;
}

/* Status Button Styling */
.btn-status {
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    min-width: 120px;
    text-align: center;
}

/* Table Container */
.table-responsive {
    overflow-x: hidden !important; /* Prevent horizontal scroll */
}

/* Table Layout */
#ordersTable {
    width: 100% !important;
    margin: 0 !important;
}

/* Column Widths */
#ordersTable th,
#ordersTable td {
    white-space: normal; /* Allow text wrapping */
}

/* Specific Column Widths */
#ordersTable th:nth-child(1), /* O.N. */
#ordersTable td:nth-child(1) {
    width: 5%;
    min-width: 50px;
}

#ordersTable th:nth-child(2), /* Customer */
#ordersTable td:nth-child(2) {
    width: 15%;
    min-width: 120px;
}

#ordersTable th:nth-child(3), /* Contact */
#ordersTable td:nth-child(3) {
    width: 12%;
    min-width: 100px;
}

#ordersTable th:nth-child(4), /* Order Date */
#ordersTable td:nth-child(4) {
    width: 15%;
    min-width: 120px;
}

#ordersTable th:nth-child(5), /* Total */
#ordersTable td:nth-child(5) {
    width: 10%;
    min-width: 80px;
}

#ordersTable th:nth-child(6), /* Order Status */
#ordersTable td:nth-child(6) {
    width: 12%;
    min-width: 100px;
}

#ordersTable th:nth-child(7), /* Payment Method */
#ordersTable td:nth-child(7) {
    width: 15%;
    min-width: 120px;
}

#ordersTable th:nth-child(8), /* More Details */
#ordersTable td:nth-child(8) {
    width: 10%;
    min-width: 100px;
}

/* Cell Content */
#ordersTable td {
    word-wrap: break-word;
    max-width: 0;
}

/* Status Button */
.btn-status {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

/* Override Bootstrap column width */
.col-lg-10 {
    flex: 0 0 100% !important;
    max-width: 100% !important;
}

.col-md-12 {
    flex: 0 0 100% !important;
    max-width: 100% !important;
}

/* Filter Styling */
.filter-container {
    margin: 20px 0;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    padding: 4px;
}

.status-filters {
    display: flex;
    gap: 10px;
    flex-wrap: nowrap;
    min-width: max-content;
}

.filter-btn {
    padding: 8px 20px;
    border: 1px solid #e0e0e0;
    border-radius: 20px;
    background: white;
    color: #666;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.filter-btn:hover {
    background-color: #f8f9fa;
    border-color: #FD6610;
    color: #FD6610;
}

.filter-btn.active {
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%);
    color: white;
    border: none;
    box-shadow: 0 2px 4px rgba(253, 102, 16, 0.2);
}

.filter-btn .badge {
    background: rgba(255, 255, 255, 0.2);
    color: inherit;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    margin-left: 4px;
}

/* Responsive */
@media (max-width: 768px) {
    .filter-container {
        margin: 15px 0;
    }
    
    .filter-btn {
        padding: 6px 16px;
        font-size: 13px;
    }
    
    .filter-btn .badge {
        padding: 2px 6px;
        font-size: 11px;
    }
}

/* Payment Method Badge */
.payment-badge {
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 13px;
    font-weight: 500;
    background-color: #e8f0fe;
    color: #1a73e8;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.payment-badge i {
    font-size: 14px;
}

/* Payment Method Types */
.payment-badge.cash {
    background-color: #e6ffe6;
    color: #0d8a0d;
}

.payment-badge.gcash {
    background-color: #f0e6ff;
    color: #6200ea;
}

/* View Details Button */
.btn-view-details {
    padding: 6px 14px;
    border-radius: 15px;
    font-size: 13px;
    font-weight: 500;
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%);
    color: white;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
}

.btn-view-details i {
    font-size: 14px;
}

.btn-view-details:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(253, 102, 16, 0.2);
    color: white;
}

/* Responsive Table */
@media (max-width: 1200px) {
    .dataTables_filter input {
        width: 200px;
    }
}

@media (max-width: 992px) {
    .filter-container {
        margin: 15px 0;
    }
    
    .filter-btn {
        padding: 6px 12px;
        font-size: 13px;
    }
    
    .payment-badge {
        padding: 4px 10px;
        font-size: 12px;
    }
    
    .btn-view-details {
        padding: 4px 10px;
        font-size: 12px;
    }
}

@media (max-width: 768px) {
    .dataTables_wrapper {
        padding: 15px;
    }
    
    .dataTables_filter input {
        width: 150px;
    }
    
    .dt-buttons {
        margin-bottom: 10px;
        width: 100%;
    }
    
    .dt-buttons .dt-button {
        width: 100%;
        margin-bottom: 5px;
    }
}

@media (max-width: 576px) {
    .filter-btn {
        padding: 4px 10px;
        font-size: 12px;
    }
    
    .filter-btn .badge {
        padding: 2px 6px;
        font-size: 11px;
    }
}

/* Only Order Status styling */
.status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    min-width: 120px;
    justify-content: center;
}

.status-badge i {
    font-size: 14px;
}

/* Status Colors */
.status-pending {
    background-color: #fff5f5;
    color: #dc3545;
    border: 1px solid #dc3545;
}

.status-progress {
    background-color: #fff8e1;
    color: #ffc107;
    border: 1px solid #ffc107;
}

.status-progress i {
    animation: spin 2s linear infinite;
}

.status-completed {
    background-color: #f0fff4;
    color: #28a745;
    border: 1px solid #28a745;
}

.status-canceled {
    background-color: #f8f9fa;
    color: #6c757d;
    border: 1px solid #6c757d;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .status-badge {
        padding: 6px 12px;
        font-size: 12px;
        min-width: 100px;
    }
    
    .status-badge i {
        font-size: 12px;
    }
}

/* Modal Styling */
.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.modal-header {
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%);
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 15px 20px;
    border: none;
}

.modal-title {
    font-weight: 600;
    font-size: 1.2rem;
    margin: 0;
}

.btn-close {
    background-color: white;
    opacity: 0.8;
    transition: all 0.2s ease;
}

.btn-close:hover {
    opacity: 1;
    transform: rotate(90deg);
}

.modal-body {
    padding: 20px;
}

.modal-dialog {
    max-width: 800px;
}

/* Responsive Modal */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 0.5rem;
    }
}

/* Add these styles for the order details table */
#orderDetailsTable {
    margin-bottom: 0;
}

#orderDetailsTable td {
    padding: 12px 15px;
    vertical-align: middle;
}

#orderDetailsTable td:first-child {
    width: 30%;
    background-color: #f8f9fa;
    font-weight: 500;
}

/* Status Dropdown in Modal */
.dropdown-toggle {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.dropdown-toggle::after {
    margin-left: 8px;
}

.dropdown-menu {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: none;
    padding: 8px;
}

.dropdown-item {
    padding: 8px 16px;
    border-radius: 5px;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: #fff5f0;
    color: #FD6610;
}

/* Print Button Styling */
.dt-buttons {
    margin-bottom: 20px;
}

.dt-button {
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%) !important;
    color: white !important;
    border: none !important;
    border-radius: 20px !important;
    padding: 8px 16px !important;
    font-size: 14px !important;
    transition: all 0.3s ease !important;
}

.dt-button:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(253, 102, 16, 0.2) !important;
}

/* Modal Body Padding */
.order-view-modal {
    padding: 20px;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    #orderDetailsTable td:first-child {
        width: 40%;
    }
    
    .dt-button {
        width: 100%;
        margin-bottom: 10px;
    }
}
</style>
